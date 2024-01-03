<div id="popup_validation_sv" class="modal" tabindex="-1" role="dialog" title="Validation de votre SV">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Veuillez confirmer la validation de la SV</h4>
            </div>
            <div class="modal-body">
                <p>Vous êtes sur le point de valider votre déclaration de production. Une fois votre déclaration validée, vous ne pourrez plus la modifier.</p>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-xs-6 text-left"><button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button></div>
                    <form action="" method="POST">
                      <div class="col-xs-6 text-right">
                        <button type="submit" data-loading-text="Validation en cours ..."
                                class="btn btn-success btn-loading"
                                id="signature_sv_popup_confirm">
                          Valider la déclaration
                        </button>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
  .modal {
    background-color: rgba(0,0,0,0.6)
  }
</style>
<script>
var initConfirmeValidationSV = function() {
    const modal_sv = document.getElementById("popup_validation_sv")
    const modal_opener = document.getElementById("valideSV")
    const modal_closer = document.querySelectorAll("[data-dismiss=modal]") || []

    modal_opener.addEventListener('click', function() {
        modal_sv.classList.add('show')
        return false;
    });

    modal_closer.forEach(function (el) {
      el.addEventListener('click', function () {
        modal_sv.classList.remove('show')
      })
    })
}
initConfirmeValidationSV()
</script>
