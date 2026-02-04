@extends('layouts.app')

@section('title', 'Fluxos')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Fluxos</h1>
            <p class="text-muted mb-0">Gerencie seus fluxos de conversação</p>
        </div>
        <a href="{{ route('dashboard.fluxes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Novo Fluxo
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($fluxes->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-project-diagram fa-4x text-muted opacity-50"></i>
                </div>
                <h4>Nenhum fluxo criado</h4>
                <p class="text-muted mb-4">Crie seu primeiro fluxo de conversação para começar a automatizar o atendimento.</p>
                <a href="{{ route('dashboard.fluxes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Criar Primeiro Fluxo
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Nome</th>
                                <th>Status</th>
                                <th>Nós</th>
                                <th>Última Edição</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fluxes as $flux)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px; background-color: rgba(59, 130, 246, 0.1);">
                                                    <i class="fas fa-project-diagram text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <a href="{{ route('dashboard.fluxes.edit', $flux) }}" class="fw-semibold text-decoration-none">
                                                    {{ $flux->name }}
                                                </a>
                                                @if($flux->data['description'] ?? false)
                                                    <div class="small text-muted">{{ Str::limit($flux->data['description'], 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($flux->status === 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @elseif($flux->status === 'draft')
                                            <span class="badge bg-warning text-dark">Rascunho</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ count($flux->data['nodes'] ?? []) }} nós</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $flux->updated_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="{{ route('dashboard.fluxes.edit', $flux) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('dashboard.fluxes.toggle-status', $flux) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-{{ $flux->status === 'active' ? 'warning' : 'success' }}"
                                                        title="{{ $flux->status === 'active' ? 'Desativar' : 'Ativar' }}">
                                                    <i class="fas fa-{{ $flux->status === 'active' ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('dashboard.fluxes.duplicate', $flux) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Duplicar">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </form>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Excluir"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $flux->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $flux->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar Exclusão</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Tem certeza que deseja excluir o fluxo <strong>{{ $flux->name }}</strong>?</p>
                                                        <p class="text-muted small mb-0">Esta ação não pode ser desfeita.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('dashboard.fluxes.destroy', $flux) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Excluir</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
