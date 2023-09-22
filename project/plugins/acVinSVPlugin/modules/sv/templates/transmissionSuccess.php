<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"> <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

    </head>
    <body style="font-family: Calibri,Arial,Verdana,Helvetica,sans-serif;">
        <div style="width: 758px; margin: 200px auto;">
            <h2 style="text-align: center; font-size: 24px; color: #848C03; font-weight: normal;">Le transfert de votre Déclaration de Récolte est en cours
            </h2>
            <img style="height: 162px;" src="/images/loader/civa2ava.gif" title="Transmission vers l'ava en cours..." />
            <form id="form_transmission" method="post" action="<?php echo $url ?>">
                <input type="hidden" name="csv" value="<?php echo $csv ?>" />
                <input type="hidden" name="pdf" value="<?php echo $pdf ?>" />
                <input type="hidden" name="typedoc" value="DR" />
            </form>
            <p style="text-align: center; color: #848C03;">Veuillez patientez...</p>
        </div>
        <script src="/js/lib/jquery-1.4.2.min.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            $('#form_transmission').submit();
        });
        </script>
    </body>
</html>
