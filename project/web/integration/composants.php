<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>Page composants</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

		<!-- Layout principal -->
		<link rel="stylesheet" type="text/css" media="screen" href="../css/global.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="../css/declaration_recolte.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="../css/jquery.ui.css" />

		<!-- Styles de mise en forme de la page -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/page_composants.css" />

		<!-- Styles génériques des composants -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/composants.css" />

		<!-- Coloration syntaxique du code -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/default.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="css/github.css" />
	</head>
	<body>
		<!-- #global -->
		<div id="global">
			<ul id="liens_evitement" class="clearfix"></ul>

			<!-- #header -->
            <div id="header" class="clearfix pngfix">
                <h1 id="logo">
                	<a href="/mon_espace_civa" title="CIVA - Comité Interprofessionnel des Vins d'Alsace - Retour à l'accueil">
                		<img src="/images/visuels/logo_civa.png" alt="CIVA - Comité Interprofessionnel des Vins d'Alsace" />
                	</a>
                </h1>

                <div id="titre_rubrique">
                    <h1>Portail pro vins d&#039;alsace</h1>
                </div>

                <div id="acces_directs">
                </div>
            </div>
            <!-- fin #header -->

			<!-- #contenu -->
			<div id="contenu">

				<div id="application_dr">

					<div class="composants">

						<!--************* Couleurs *************-->

						<h2 class="h2_composants">Couleurs</h2>

						<h3 class="h3_composants">Titres</h3>
						<div class="couleur c-dcdfa9">#dcdfa9</div>
						<div class="couleur c-848c03">#848c03</div>

						<h3 class="h3_composants">Boutons</h3>

						<!-- bouton primary -->
						<div class="couleur c-ffe0a5">#ffe0a5</div>
						<div class="couleur c-ff8500">#ff8500</div>

						<!-- bouton danger  -->
						<div class="couleur c-fa9090">#fa9090</div>
						<div class="couleur c-b70101">#b70101</div>

						<!-- bouton success -->
						<div class="couleur c-e4e4b5">#e4e4b5</div>
						<div class="couleur c-bbcc00">#bbcc00</div>

						<!--************* Titres *************-->

						<h2 class="h2_composants">Titres</h2>

						<h1><span>Titre h1</span></h1><br />

						<h2><span>Titre h2</span></h2>

<pre><code class="language-html">&lt;h1&gt;&lt;span&gt;Titre h1&lt;/span&gt;&lt;/h1&gt; &lt;!-- ou --&gt; &lt;span class="h1"&gt;&lt;span&gt;Titre h1&lt;/span&gt;&lt;/span&gt;
&lt;h2&gt;&lt;span&gt;Titre h2&lt;/span&gt;&lt;/h2&gt; &lt;!-- ou --&gt; &lt;span class="h2"&gt;&lt;span&gt;Titre h2&lt;/span&gt;&lt;/span&gt;
</code></pre>

						<!--************* Grille *************-->

						<h2 class="h2_composants">Grille</h2>

						<div class="row">
							<div class="col-1">.col-1</div>
							<div class="col-1">.col-1</div>
							<div class="col-1">.col-1</div>
						</div>

						<div class="row">
							<div class="col-2">.col-2</div>
							<div class="col-1">.col-1</div>
						</div>

						<div class="row">
							<div class="col-3">.col-3</div>
						</div>

<pre><code class="language-html">&lt;div class="row"&gt;
	&lt;div class="col-1"&gt;.col-1&lt;/div&gt;
	&lt;div class="col-1"&gt;.col-1&lt;/div&gt;
	&lt;div class="col-1"&gt;.col-1&lt;/div&gt;
&lt;/div&gt;

&lt;div class="row"&gt;
	&lt;div class="col-2"&gt;.col-2&lt;/div&gt;
	&lt;div class="col-1"&gt;.col-1&lt;/div&gt;
&lt;/div&gt;

&lt;div class="row"&gt;
	&lt;div class="col-3"&gt;.col-3&lt;/div&gt;
&lt;/div&gt;</code></pre>

						<!--************* Grille *************-->

						<h2 class="h2_composants">Tableaux</h2>
						
						<h3 class="h3_composants">Tableaux normaux</h3>

						<table class="table">
							<thead>
								<tr>
									<th>Type</th>
									<th>N°</th>
									<th>Date</th>
									<th>Soussignés</th>
									<th>Statut</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Lorem ipsum dolor sit amet.</td>
									<td>Obcaecati magni fugit ab ex.</td>
									<td>Ab temporibus ratione eveniet officia.</td>
									<td>Dolor, deserunt asperiores voluptatibus minus.</td>
									<td>Fugiat fuga voluptatum non sit!</td>
									<td>Mollitia obcaecati eius enim nostrum.</td>
								</tr>
								<tr>
									<td>Lorem ipsum dolor sit amet.</td>
									<td>Voluptatum, tempore iure quasi ea?</td>
									<td>Unde quasi minus rerum reprehenderit!</td>
									<td>Ea similique earum veniam quod.</td>
									<td>Ea tempora eius fugiat perferendis!</td>
									<td>Laborum, facilis quos repudiandae iusto.</td>
								</tr>
								<tr>
									<td>Lorem ipsum dolor sit amet.</td>
									<td>Voluptatum, tempore iure quasi ea?</td>
									<td>Unde quasi minus rerum reprehenderit!</td>
									<td>Ea similique earum veniam quod.</td>
									<td>Ea tempora eius fugiat perferendis!</td>
									<td>Laborum, facilis quos repudiandae iusto.</td>
								</tr>
							</tbody>
						</table>
						
						<pre><code class="language-html">&lt;table class="table"&gt;...&lt;/table&gt;</code></pre>


						<h3 class="h3_composants">Tableaux avec cellules grises</h3>
						<table class="table">
							<thead>
								<tr>
									<th class="no-bg">Type</th>
									<th class="no-bg">N°</th>
									<th>Date</th>
									<th>Soussignés</th>
									<th>Statut</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Lorem ipsum dolor sit amet.</td>
									<td>Obcaecati magni fugit ab ex.</td>
									<td>Ab temporibus ratione eveniet officia.</td>
									<td>Dolor, deserunt asperiores voluptatibus minus.</td>
									<td>Fugiat fuga voluptatum non sit!</td>
									<td>Mollitia obcaecati eius enim nostrum.</td>
								</tr>
								<tr>
									<td>Lorem ipsum dolor sit amet.</td>
									<td>Voluptatum, tempore iure quasi ea?</td>
									<td>Unde quasi minus rerum reprehenderit!</td>
									<td>Ea similique earum veniam quod.</td>
									<td>Ea tempora eius fugiat perferendis!</td>
									<td>Laborum, facilis quos repudiandae iusto.</td>
								</tr>
								<tr>
									<td>Lorem ipsum dolor sit amet.</td>
									<td>Voluptatum, tempore iure quasi ea?</td>
									<td>Unde quasi minus rerum reprehenderit!</td>
									<td>Ea similique earum veniam quod.</td>
									<td>Ea tempora eius fugiat perferendis!</td>
									<td>Laborum, facilis quos repudiandae iusto.</td>
								</tr>
							</tbody>
						</table>

<pre><code class="language-html">&lt;table class="table"&gt;
	&lt;thead&gt;
		&lt;tr&gt;
			&lt;th class="no-bg"&gt;Type&lt;/th&gt;
			&lt;th class="no-bg"&gt;N°&lt;/th&gt;
			&lt;th&gt;Date&lt;/th&gt;
			&lt;th&gt;Soussignés&lt;/th&gt;
			&lt;th&gt;Statut&lt;/th&gt;
			&lt;th&gt;Actions&lt;/th&gt;
		&lt;/tr&gt;
	&lt;/thead&gt;

...
&lt;/table&gt;
</code></pre>
						
						<!--************* Boutons *************-->

						<h2 class="h2_composants">Boutons</h2>

						<h3 class="h3_composants">Types</h3>

						<div>
							<a href="#" class="btn btn-default">Bouton default</a>
							<a href="#" class="btn btn-success">Bouton success</a>
							<a href="#" class="btn btn-primary">Bouton primary</a>
							<a href="#" class="btn btn-danger">Bouton danger</a>
						</div>

						<pre><code class="language-html">&lt;a href="#" class="btn btn-sm btn-default"&gt;Bouton default&lt;/a&gt;
&lt;a href="#" class="btn btn-success"&gt;Bouton success&lt;/a&gt;
&lt;a href="#" class="btn btn-primary"&gt;Bouton primary&lt;/a&gt;
&lt;a href="#" class="btn btn-danger"&gt;Bouton danger&lt;/a&gt;</code></pre>

						<h3 class="h3_composants">Disabled</h3>

						<div>
							<button class="btn btn-default" disabled="disabled">Bouton disabled</button>
						</div>

<pre><code class="language-html">&lt;button class="btn btn-default" disabled="disabled"&gt;Bouton disabled&lt;/button&gt; 
&lt;!-- ou --&gt; 
&lt;button class="btn btn-default disabled"&gt;Bouton disabled&lt;/button&gt;</code></pre>

						<h3 class="h3_composants">Tailles</h3>

						<div>
							<a href="#" class="btn btn-default btn-sm">Petit bouton</a>
							<a href="#" class="btn btn-default">Moyen bouton</a>
							<a href="#" class="btn btn-default btn-lg">Grand bouton</a>
						</div>

						<pre><code class="language-html">&lt;a href="#" class="btn btn-default btn-sm"&gt;Petit bouton&lt;/a&gt;
&lt;a href="#" class="btn btn-default"&gt;Moyen bouton&lt;/a&gt;
&lt;a href="#" class="btn btn-default btn-lg"&gt;Grand bouton&lt;/a&gt;</code></pre>
			
						
						<h3 class="h3_composants">Flèches</h3>
						
						<div>
							<a href="#" class="btn btn-primary btn-arrow-l">Bouton flèche gauche</a>
							<a href="#" class="btn btn-default btn-arrow-r">Bouton flèche droite</a>
						</div>
						
<pre><code class="language-html">&lt;a href="#" class="btn btn-primary btn-arrow-l"&gt;Bouton flèche gauche&lt;/a&gt;
&lt;a href="#" class="btn btn-default btn-arrow-r"&gt;Bouton flèche droite&lt;/a&gt;</code></pre>

						<h3 class="h3_composants">Icônes</h3>

						<div>
							<a href="#" class="btn btn-primary btn-icon-l"><img src="/images/pictos/pi_supprimer.png" alt="" />Bouton icône gauche</a>
							<a href="#" class="btn btn-primary btn-icon-r">Bouton icône droite<img src="/images/pictos/pi_visualiser.png" alt="" /></a>
						</div>
<pre><code class="language-html">&lt;a href="#" class="btn btn-primary btn-icon-l"&gt;&lt;img src="/images/pictos/pi_supprimer.png" alt="" /&gt;Bouton icône gauche&lt;/a&gt;
&lt;a href="#" class="btn btn-primary btn-icon-r"&gt;Bouton icône droite&lt;img src="/images/pictos/pi_visualiser.png" alt="" /&gt;&lt;/a&gt;</code></pre>					

						<!--************* Onglets *************-->

						<h2 class="h2_composants">Onglets</h2>

						<!-- <ul class="nav nav-tabs">
							<li><a href="#">Accueil</a></li>
							<li><a href="#">Alsace Récolte</a></li>
							<li><a href="#">Alsace Contrats</a></li>
						</ul> -->


						<!--************* Blocs *************-->

						<h2 class="h2_composants">Blocs</h2>

						<div class="panel panel-default">
							<div class="panel-body">
								Bloc normal
							</div>
						</div>

						<h2 class="h2_composants">Blocs avec titre</h2>

						<div class="panel panel-default">
							<div class="panel-heading">Titre</div>
							<div class="panel-body">
								Bloc avec titre
							</div>
						</div>

					</div>
				</div>
			</div>
			<!-- fin #contenu -->
		</div>
		<!-- fin #global -->

		<!-- Coloration syntaxique -->
		<script type="text/javascript" src="js/highlight.pack.js"></script>
		<script type="text/javascript">
			hljs.configure({tabReplace: ' '}); // 2 spaces
			hljs.initHighlightingOnLoad();
		</script>
	</body>
</html>