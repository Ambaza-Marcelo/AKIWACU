<div class="modal fade" id="journalPaieModal" tabindex="-1" role="dialog" aria-labelledby="journalPaieModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="journalPaieModalLabel">Nouveau Journal de Paie</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.hr-journal-paies.store') }}" method="POST">
        @csrf
      <div class="modal-body">
                    <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="title">Titre du Journal<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="title" placeholder="Ex:Journal de Paie du 01 Janvier au 31 Janvier" required>
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="date_debut">Date Début<span class="text-danger"></span></label>
                                        <input autofocus type="date" class="form-control" name="date_debut" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="date_fin">Date Fin<span class="text-danger"></span></label>
                                        <input autofocus type="date" class="form-control" name="date_fin" required>
                                    </div>
                                </div>
                        </div>
                    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary">@lang('messages.save')</button>
      </div>
      </form>
    </div>
  </div>
</div>
