<div class="modal fade" id="congePayeModal" tabindex="-1" role="dialog" aria-labelledby="congePayeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="congePayeModalLabel">Nouvelle Fonction</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.hr-conge-payes.store') }}" method="POST">
        @csrf
      <div class="modal-body">
          <div class="form-group has-feedback">
                <label for="session">Session<span class="text-danger"></span></label>
                <input autofocus type="number" class="form-control" name="session" placeholder="Ex:2023" required min="2023">
            </div>
          <div class="form-group has-feedback">
                <label for="nbre_jours">Nombre de Jours<span class="text-danger"></span></label>
                <input autofocus type="number" class="form-control" name="nbre_jours" placeholder="Ex:22" required min="20" max="25">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
        <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
      </div>
      </form>
    </div>
  </div>
</div>