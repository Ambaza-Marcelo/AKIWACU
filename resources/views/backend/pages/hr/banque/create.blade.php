<div class="modal fade" id="banqueModal" tabindex="-1" role="dialog" aria-labelledby="banqueModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="banqueModalLabel">Nouvelle Banque</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.hr-banques.store') }}" method="POST">
        @csrf
      <div class="modal-body">
          <div class="form-group">
            <label for="name" class="col-form-label">Designation *:</label>
            <input type="text" name="name" class="form-control" id="name">
          </div>
          <div class="form-group">
            <label for="address" class="col-form-label">Adresse *:</label>
            <input type="text" name="address" class="form-control" id="address">
          </div>
          <div class="form-group">
            <label for="currency" class="col-form-label">Monnaie:</label>
            <input type="text" name="currency" class="form-control" id="currency">
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