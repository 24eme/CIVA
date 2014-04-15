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
                	<a href="/mon_espace_civa" title="CIVA - Conseil Interprofessionnel des Vins d'Alsace - Retour à l'accueil">
                		<img src="/images/visuels/logo_civa.png" alt="CIVA - Conseil Interprofessionnel des Vins d'Alsace" />
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

						<h2>Couleurs</h2>

						<div class="couleur c-dcdfa9"></div>
						<div class="couleur c-848C03"></div>

						<h2>Tableaux</h2>
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
							</tbody>
						</table>
						
						<pre>
							<code class="language-html"><!--
								-->&lt;table class="table"&gt;...&lt;/table&gt;<!--
							--></code>
						</pre>
						
						<h2>Boutons</h2>
						
						<br />
						
						<h3>Boutons petits</h3>
						
						<div>
							<a href="#" class="btn_majeur btn_petit btn_vert btn_fleche_d">Bouton vert</a>
							<a href="#" class="btn_majeur btn_petit btn_jaune">Bouton jaune</a>
							<a href="#" class="btn_majeur btn_petit btn_rouge btn_fleche_g">Bouton rouge</a>
						</div>
						
						<pre>
							<code class="language-html"><!--
								-->&lt;a href="#" class="btn_majeur btn_petit btn_vert btn_fleche_d"&gt;Bouton vert&lt;/a&gt;<!-- 
								--><br />&lt;a href="#" class="btn_majeur btn_petit btn_jaune"&gt;Bouton jaune&lt;/a&gt;<!-- 
								--><br />&lt;a href="#" class="btn_majeur btn_petit btn_rouge btn_fleche_g"&gt;Bouton rouge&lt;/a&gt;<!--
							--></code>
						</pre>
						
						<br />
						<br />
						
						
						<h3>Boutons moyens</h3>
						
						<div>
							<a href="#" class="btn_majeur btn_vert btn_fleche_d">Bouton vert</a>
							<a href="#" class="btn_majeur btn_jaune">Bouton jaune</a>
							<a href="#" class="btn_majeur btn_rouge btn_fleche_g">Bouton rouge</a>
						</div>
						
						<pre>
							<code class="language-html"><!--
								-->&lt;a href="#" class="btn_majeur btn_vert btn_fleche_d"&gt;Bouton vert&lt;/a&gt;<!-- 
								--><br />&lt;a href="#" class="btn_majeur btn_jaune"&gt;Bouton jaune&lt;/a&gt;<!-- 
								--><br />&lt;a href="#" class="btn_majeur btn_rouge btn_fleche_g"&gt;Bouton rouge&lt;/a&gt;	<!--
							--></code>
						</pre>
						
						<br />
						<br />
						
						<h3>Boutons grands</h3>
						
						<div>
							<a href="#" class="btn_majeur btn_grand btn_vert btn_fleche_d">Bouton vert</a>
							<a href="#" class="btn_majeur btn_grand btn_jaune">Bouton jaune</a>
							<a href="#" class="btn_majeur btn_grand btn_rouge btn_fleche_g">Bouton rouge</a>
						</div>
						
						<pre>
							<code class="language-html"><!--
								-->&lt;a href="#" class="btn_majeur btn_grand btn_vert btn_fleche_d"&gt;Bouton vert&lt;/a&gt;<!-- 
								--><br />&lt;a href="#" class="btn_majeur btn_grand btn_jaune"&gt;Bouton jaune&lt;/a&gt;<!-- 
								--><br />&lt;a href="#" class="btn_majeur btn_rouge btn_fleche_g"&gt;Bouton rouge&lt;/a&gt;<!-- 
							--></code>
						</pre>
					</div>
				</div>
			</div>
			<!-- fin #contenu -->
		</div>
		<!-- fin #global -->

		<script type="text/javascript" src="js/highlight.pack.js"></script>
		<script type="text/javascript">hljs.initHighlightingOnLoad();</script>
	</body>
</html>