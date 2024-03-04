<div class="modal fade" id="typeCongeModal" tabindex="-1" role="dialog" aria-labelledby="typeCongeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="typeCongeModalLabel">Nouveau Type Congé</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.hr-type-conges.store') }}" method="POST">
        @csrf
      <div class="modal-body">
          <div class="form-group">
            <label for="libelle" class="col-form-label">Designation:</label>
            <input type="text" name="libelle" class="form-control" id="libelle">
          </div>
          <div class="form-group">
            <label for="description" class="col-form-label">Description:</label>
            <input type="text" name="description" class="form-control" id="description">
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