@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-3">Operações</h2>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#operationModal">Nova Operação</button>
        </div>
    </div>
    <div id="alert-area"></div>
    <table class="table table-hover align-middle" id="operations-table">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody>
            <!-- Conteúdo dinâmico via JS -->
        </tbody>
    </table>

    <!-- Modal de cadastro de operação -->
    <div class="modal fade" id="operationModal" tabindex="-1" aria-labelledby="operationModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="operationModalLabel">Nova Operação</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="operation-form">
              <div class="mb-3">
                <label for="operation-customer" class="form-label">Cliente</label>
                <select class="form-select" id="operation-customer" required></select>
              </div>
              <div class="mb-3">
                <label for="operation-type" class="form-label">Tipo</label>
                <select class="form-select" id="operation-type" required>
                  <option value="deposit">Depósito</option>
                  <option value="withdraw">Saque</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="operation-amount" class="form-label">Valor</label>
                <input type="number" step="0.01" class="form-control" id="operation-amount" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Enviar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showAlert(msg, type = 'success') {
    document.getElementById('alert-area').innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
    setTimeout(() => { document.getElementById('alert-area').innerHTML = ''; }, 4000);
}

function fetchOperations() {
    fetch('/api/operations')
        .then(r => r.json())
        .then(data => {
            let tbody = document.querySelector('#operations-table tbody');
            tbody.innerHTML = '';
            data.data.forEach(o => {
                tbody.innerHTML += `<tr><td>${o.id}</td><td>${o.customer_id}</td><td>${o.type === 'deposit' ? 'Depósito' : 'Saque'}</td><td>R$ ${parseFloat(o.amount).toFixed(2)}</td><td>${o.status}</td><td>${o.result ?? ''}</td></tr>`;
            });
        });
}

function fetchCustomersSelect() {
    fetch('/api/customers')
        .then(r => r.json())
        .then(data => {
            let select = document.getElementById('operation-customer');
            select.innerHTML = '';
            data.data.forEach(c => {
                select.innerHTML += `<option value="${c.id}">${c.name} (${c.email})</option>`;
            });
        });
}

document.querySelector('[data-bs-target="#operationModal"]').onclick = function() {
    fetchCustomersSelect();
    document.getElementById('operation-form').reset();
};

document.getElementById('operation-form').onsubmit = function(e) {
    e.preventDefault();
    const customer_id = document.getElementById('operation-customer').value;
    const type = document.getElementById('operation-type').value;
    const amount = document.getElementById('operation-amount').value;
    fetch('/api/operations', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ customer_id, type, amount })
    })
    .then(async r => {
        if (r.ok) {
            showAlert('Operação enviada!');
            bootstrap.Modal.getInstance(document.getElementById('operationModal')).hide();
            fetchOperations();
        } else {
            const err = await r.json();
            showAlert('Erro: ' + (err.message || 'Verifique os dados.'), 'danger');
        }
    });
};

setInterval(fetchOperations, 5000);
fetchOperations();
</script>
@endsection
