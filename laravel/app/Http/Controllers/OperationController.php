<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Customer;
use App\Http\Resources\OperationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// AWS SDK
use Aws\Sqs\SqsClient;

class OperationController extends Controller {
    
    public function index()
    {
        return \App\Http\Resources\OperationResource::collection(\App\Models\Operation::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:withdraw,deposit',
            'amount' => 'required|numeric',
        ]);

        // Validação de valor
        if ($data['amount'] <= 0) {
            return response()->json(['message' => 'O valor deve ser positivo.'], 422);
        }

        $customer = Customer::find($data['customer_id']);
        if ($data['type'] === 'withdraw' && $customer->balance < $data['amount']) {
            return response()->json(['message' => 'Saldo insuficiente para saque.'], 422);
        }

        // Cria operation local (status pending)
        $operation = Operation::create([
            'customer_id' => $data['customer_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'status' => 'pending',
        ]);

        // Envia para SQS
        try {
            $sqsConfig = [
                'region' => env('AWS_DEFAULT_REGION', 'sa-east-1'),
                'version' => 'latest',
            ];
            $awsKey = env('AWS_ACCESS_KEY_ID');
            $awsSecret = env('AWS_SECRET_ACCESS_KEY');
            if ($awsKey && $awsSecret) {
                $sqsConfig['credentials'] = [
                    'key' => $awsKey,
                    'secret' => $awsSecret,
                ];
            }
            $sqs = new SqsClient($sqsConfig);
            $queueUrl = env('SQS_QUEUE_URL');
            $payload = [
                'id' => $operation->id,
                'customer_id' => $operation->customer_id,
                'type' => $operation->type,
                'amount' => $operation->amount,
            ];
            $sqs->sendMessage([
                'QueueUrl' => $queueUrl,
                'MessageBody' => json_encode($payload),
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar para SQS: ' . $e->getMessage());
            Log::error('URL SQS!: ' . env('AWS_SECRET_ACCESS_KEY') );
            return response()->json(['message' => 'Erro ao enviar operação para processamento.'], 500);
        }

        return new OperationResource($operation);
    }
    // Endpoint para lambda aplicar operação (deposit/withdraw) e atualizar status/result
    public function apply(Request $request, $id)
    {
        $operation = Operation::findOrFail($id);
        if ($operation->status !== 'pending' && $operation->status !== 'processing') {
            return response()->json(['message' => 'Operação já processada.'], 400);
        }

        // Simula delay de 5 segundos
        sleep(5);

        if ($operation->status === 'pending') {
            $operation->status = 'processing';
            $operation->save();
        }

        $customer = Customer::findOrFail($operation->customer_id);
        $result = null;
        if ($operation->type === 'withdraw') {
            if ($customer->balance < $operation->amount) {
                $operation->status = 'fail';
                $operation->result = 'Saldo insuficiente';
                $operation->save();
                return new OperationResource($operation);
            }
            $customer->balance -= $operation->amount;
            $result = 'Saque realizado';
        } else {
            $customer->balance += $operation->amount;
            $result = 'Depósito realizado';
        }
        $customer->save();
        $operation->status = 'done';
        $operation->result = $result;
        $operation->save();
        return new OperationResource($operation);
    }

    public function show($id)
    {
        $operation = Operation::findOrFail($id);
        return new OperationResource($operation);
    }
}
