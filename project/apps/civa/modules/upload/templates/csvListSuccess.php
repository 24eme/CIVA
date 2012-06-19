 <h2 class="titre_principal">Statistiques</h2>

    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
    <ul>
    <?php foreach($csv as $c) {
    echo "<li>";
    print_r($c);
    echo "</li>";
  }?>
    </ul>
        </div>
    </div>