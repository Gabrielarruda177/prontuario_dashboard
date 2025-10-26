@extends('medico.templates.medicoTemplate')

@section('title', 'Perfil do Médico')

@section('content')
<link rel="stylesheet" href="{{ asset('css/medico/perfilMedico.css') }}">

{{-- 🔥 CORREÇÃO: Buscar o médico com o usuário relacionado --}}
@php 
    $usuario = auth()->user();
    $medico = $usuario ? App\Models\Medico::with('usuario')->where('id_usuarioFK', $usuario->idUsuarioPK)->first() : null;
@endphp

<main class="main-dashboard">
  <div class="cadastrar-container">
    <div class="cadastrar-header">
      <i class="bi bi-person-circle icon"></i> 
      <h1>Perfil do Médico</h1>
    </div>

    {{-- Mensagens de erro --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário com ID para o JavaScript --}}
    <form id="profileForm" action="{{ route('medico.perfil.update') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      {{-- Bloco da Foto --}}
      <div class="foto-upload-container">
        <label for="foto" class="foto-upload-label">
          <div class="box-foto">
            <img id="preview-img"
              src="{{ $medico && $medico->foto ? asset('storage/fotos/' . $medico->foto) : asset('img/usuario-de-perfil.png') }}"
              alt="Foto atual">
          </div>

          {{-- Overlay para Alterar Foto --}}
          <div class="overlay">
            <i class="bi bi-camera"></i>
            <span>Alterar Foto</span>
          </div>
        </label>
        <input type="file" id="foto" name="foto" accept="image/*" hidden onchange="previewFoto(event)">
      </div>

      {{-- Campos de Dados --}}
      <div class="input-group">
        <input type="text" name="nomeMedico" id="nomeMedico" placeholder="Nome Completo"
               value="{{ old('nomeMedico', $medico->nomeMedico ?? '') }}" required>
      </div>

      <div class="input-group">
        <input type="text" name="crmMedico" id="crmMedico" placeholder="CRM"
               value="{{ $medico->crmMedico ?? '' }}" disabled title="Campo não editável">
      </div>

      {{-- 🔥 MUDOU: emailUsuario em vez de emailMedico --}}
      <div class="input-group">
        <input type="email" name="emailUsuario" id="emailUsuario" placeholder="E-mail"
               value="{{ old('emailUsuario', $medico->usuario->emailUsuario ?? '') }}" required>
      </div>

      {{-- Botões de Ação --}}
      <div class="button-group">
        <a href="{{ route('medico.seguranca') }}" class="btn-trocar-senha">Trocar Senha</a>
        {{-- Botão alterado: type="button" e chama o modal --}}
        <button type="button" class="save-button" onclick="showConfirmationModal()">Salvar Alterações</button>
      </div>
    </form>
  </div>
</main>

{{-- ======================================================== --}}
{{-- HTML DOS MODAIS ADICIONADO AQUI                     --}}
{{-- ======================================================== --}}

<div id="confirmationModal" class="modal-overlay">
    <div class="modal-box">
        <i class="bi bi-exclamation-triangle-fill modal-icon icon-warning"></i>
        <h2>Confirmar Alterações</h2>
        <p>Deseja realmente salvar as alterações no seu perfil?</p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="hideConfirmationModal()">Cancelar</button>
            <button type="button" class="modal-btn modal-btn-confirm" onclick="submitProfileForm()">Confirmar</button>
        </div>
    </div>
</div>

@if(session('success'))
<div id="successModal" class="modal-overlay show">
    <div class="modal-box">
        <i class="bi bi-check-circle-fill modal-icon icon-success"></i>
        <h2>Sucesso!</h2>
        <p>{{ session('success') }}</p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-confirm" onclick="hideSuccessModal()">Fechar</button>
        </div>
    </div>
</div>
@endif

{{-- ======================================================== --}}
{{-- JAVASCRIPT DE CONTROLE (FOTO E MODAIS)              --}}
{{-- ======================================================== --}}
<script>
// Função para preview da imagem
function previewFoto(event) {
  const input = event.target;
  const preview = document.getElementById('preview-img');

  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Pega os elementos do DOM para os modais
const profileForm = document.getElementById('profileForm');
const confirmationModal = document.getElementById('confirmationModal');
const successModal = document.getElementById('successModal');

// --- Funções para o Modal de Confirmação ---
function showConfirmationModal() {
    if (confirmationModal) confirmationModal.classList.add('show');
}

function hideConfirmationModal() {
    if (confirmationModal) confirmationModal.classList.remove('show');
}

function submitProfileForm() {
    hideConfirmationModal();
    if (profileForm) profileForm.submit();
}

// --- Funções para o Modal de Sucesso ---
function hideSuccessModal() {
    if (successModal) successModal.classList.remove('show');
}

// Opcional: Fechar o modal clicando fora da caixa
window.onclick = function(event) {
    if (event.target == confirmationModal) {
        hideConfirmationModal();
    }
    if (event.target == successModal) {
        hideSuccessModal();
    }
}
</script>
@endsection