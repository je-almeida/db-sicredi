@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-3">Clientes</h2>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#customerModal">Novo Cliente</button>
        </div>
    </div>
    <div id="alert-area"></div>
    <table class="table table-hover align-middle" id="customers-table">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Saldo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Conteúdo dinâmico via JS -->
        </tbody>
    </table>

    <!-- Modal de cadastro/edição -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="customerModalLabel">Novo Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="customer-form">
              <input type="hidden" id="customer-id">
              <div class="mb-3">
                <label for="customer-name" class="form-label">Nome</label>
                <input type="text" class="form-control" id="customer-name" required>
              </div>
              <div class="mb-3">
                <label for="customer-email" class="form-label">Email</label>
                <input type="email" class="form-control" id="customer-email" required>
              </div>
              <div class="mb-3">
                <label for="customer-balance" class="form-label">Saldo</label>
                <input type="number" step="0.01" class="form-control" id="customer-balance" required>
              </div>
              <button type="submit" class="btn btn-primary w-100" id="save-btn">Salvar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let editingId = null;
const modal = new bootstrap.Modal(document.getElementById('customerModal'));

function showAlert(msg, type = 'success') {
    document.getElementById('alert-area').innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
    setTimeout(() => { document.getElementById('alert-area').innerHTML = ''; }, 4000);
}

function fetchCustomers() {
    fetch('/api/customers')
        .then(r => r.json())
        .then(data => {
            let tbody = document.querySelector('#customers-table tbody');
            tbody.innerHTML = '';
            data.data.forEach(c => {
                tbody.innerHTML += `<tr>
                    <td>${c.id}</td>
                    <td>${c.name}</td>
                    <td>${c.email}</td>
                    <td>R$ ${parseFloat(c.balance).toFixed(2)}</td>
                    <td>
                        <button class='btn btn-sm btn-primary me-1' onclick='editCustomer(${JSON.stringify(c)})'>Editar</button>
                        <button class='btn btn-sm btn-danger' onclick='deleteCustomer(${c.id})'>Excluir</button>
                    </td>
                </tr>`;
            });
        });
}

function editCustomer(c) {
    editingId = c.id;
    document.getElementById('customer-id').value = c.id;
    document.getElementById('customer-name').value = c.name;
    document.getElementById('customer-email').value = c.email;
    document.getElementById('customer-balance').value = c.balance;
    document.getElementById('customerModalLabel').innerText = 'Editar Cliente';
    modal.show();
}

function deleteCustomer(id) {
    if (!confirm('Deseja realmente excluir este cliente?')) return;
    fetch(`/api/customers/${id}`, { method: 'DELETE' })
        .then(r => {
            if (r.ok) {
                showAlert('Cliente excluído com sucesso!');
                fetchCustomers();
            } else {
                showAlert('Erro ao excluir cliente.', 'danger');
            }
        });
}

document.getElementById('customer-form').onsubmit = function(e) {
    e.preventDefault();
    const id = document.getElementById('customer-id').value;
    const name = document.getElementById('customer-name').value;
    const email = document.getElementById('customer-email').value;
    const balance = document.getElementById('customer-balance').value;
    const payload = { name, email, balance };
    let url = '/api/customers', method = 'POST';
    if (id) {
        url += `/${id}`;
        method = 'PUT';
    }
    fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(async r => {
        if (r.ok) {
            showAlert(id ? 'Cliente atualizado!' : 'Cliente cadastrado!');
            modal.hide();
            fetchCustomers();
            document.getElementById('customer-form').reset();
            document.getElementById('customerModalLabel').innerText = 'Novo Cliente';
            editingId = null;
        } else {
            const err = await r.json();
            showAlert('Erro: ' + (err.message || 'Verifique os dados.'), 'danger');
        }
    });
};

document.querySelector('[data-bs-target="#customerModal"]').onclick = function() {
    editingId = null;
    document.getElementById('customer-form').reset();
    document.getElementById('customer-id').value = '';
    document.getElementById('customerModalLabel').innerText = 'Novo Cliente';
};

setInterval(fetchCustomers, 5000);
fetchCustomers();
</script>
@endsection
