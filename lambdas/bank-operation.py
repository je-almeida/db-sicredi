

import time
import os
import json
import requests
import boto3
from dotenv import load_dotenv

load_dotenv(dotenv_path=os.path.join(os.path.dirname(__file__), '.env'))

API_URL = os.getenv('API_URL', 'http://localhost/api')
AWS_REGION = os.getenv('AWS_REGION')
SQS_QUEUE_URL = os.getenv('SQS_QUEUE_URL')

sqs = boto3.client('sqs', region_name=AWS_REGION)

def receive_sqs_message():
    resp = sqs.receive_message(
        QueueUrl=SQS_QUEUE_URL,
        MaxNumberOfMessages=1,
        WaitTimeSeconds=10
    )
    msgs = resp.get('Messages', [])
    if msgs:
        return msgs[0]
    return None

def delete_sqs_message(receipt_handle):
    sqs.delete_message(QueueUrl=SQS_QUEUE_URL, ReceiptHandle=receipt_handle)

def update_operation_status(op_id, status, result=None):
    payload = {'status': status}
    if result is not None:
        payload['result'] = result
    requests.put(f"{API_URL}/operations/{op_id}/status", json=payload)

def process_operation(op):
    op_id = op['id']
    update_operation_status(op_id, 'processing')
    time.sleep(5)  # simula delay
    try:
        resp = requests.get(f"{API_URL}/customers/{op['customer_id']}")
        if resp.status_code != 200:
            update_operation_status(op_id, 'fail', 'Customer not found')
            return
        customer = resp.json()
        balance = float(customer['balance'])
        print(f"Current balance: {balance}")
        if op['type'] == 'withdraw':
            if balance < float(op['amount']):
                update_operation_status(op_id, 'fail', 'Insufficient funds')
                return
            new_balance = balance - float(op['amount'])
        else:
            new_balance = balance + float(op['amount'])
        requests.put(f"{API_URL}/customers/{op['customer_id']}", json={'balance': new_balance})
        update_operation_status(op_id, 'done', f'New balance: {new_balance}')
    except Exception as e:
        print(f"Error processing operation {op_id}: {e}")
        update_operation_status(op_id, 'fail', str(e))


# Removido main() duplicado. Usar apenas a versão abaixo.

import sys

def main():
      print("Processing a single message...")
      msg = receive_sqs_message()
      if msg:
          print("Message received, processing...")
          try:
              body = json.loads(msg['Body'])
              process_operation(body)
          finally:
              delete_sqs_message(msg['ReceiptHandle'])
      return
main()
