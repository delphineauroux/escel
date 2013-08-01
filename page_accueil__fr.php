<?php
	session_start(); # Creating or updating session.

	include_once("inc/debug.inc.php");
	unset($_SESSION["debug"]);

	# Initializing locale
	if (!isset($_GET["lang"]))
	{
		header("Location: $PHP_SELF?lang=fr");
	}
	else
	{ini_set("intl.default_locale", $_GET["lang"]);}

	# Initializing connection status
	if (!isset($_SESSION["connecte"]))
	{$_SESSION["connecte"] = false;}
	$_SESSION["connecte"] = false;
?>
 <!DOCTYPE html>
 <html>
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Ce site propose plusieurs exp&eacute;rimentations r&eacute;alisables
									  soi-m&ecirc;me en ligne, en se basant sur les recherches
									  effectu&eacute;es en sciences cognitives." />
	<meta name="keywords" content="sciences cognitives, expériences, expérimentations, en ligne" />
	<meta name="author" content="Nicolaï LABORDE--SCHUMACHER" />

	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="stylesheet" type="text/css" href="easter_eggs.css" />

	<title>Exp&eacute;rimentations de sciences cognitives en ligne</title>
</head>
<body class="ares-body">
	<ul>
		<li class="ares-button" style="cursor: default; position: absolute; top: 0; left: 0; width: 30px; height: 30px; z-index: 10000; margin: 0; padding: 0; opacity: 0;"></li>
		<li class="easteregg_mona"></li>
	</ul>
	<?php
		# DEBUG -> BEGIN
		if (isset($_SESSION["debug"]))
		{Debug::print_variable_data("connected", $_SESSION["connected"]);}
		# DEBUG -> END
	?>
	<header>
		<h3 class="ares-heading">Exp&eacute;rimentation de sciences cognitives en ligne</h3>
		<div class="ares-navbar">
			<span class="brand" onclick="javascript:document.getElementById('sciences_cognitives__content').style.height = '0';
													document.getElementById('fonctionnement_site__content').style.height = '0';
													document.getElementById('participation__content').style.height = '0';">Accueil</span>
			<div class="nav">
				<a onclick="javascript:section_clicked('sciences_cognitives');" href="#sciences_cognitives">Sciences cognitives</a>
				<a onclick="javascript:section_clicked('fonctionnement_site');" href="#fonctionnement_site">Fonctionnement du site</a>
				<a onclick="javascript:section_clicked('participation');" href="#participation">Participation aux exp&eacute;rimentations</a>
				<a onclick="javascript:document.getElementById('modal_aide').style.display = 'block';" href="#aide">Aide</a>
			</div>
		</div>
		<div id="modal_aide" class="ares-modal information attention" style="display: none;">
			<div class="header">
				<span class="ares-button close" onclick="javascript:this.parentNode.parentNode.style.display = 'none';"></span>
				<h3 class="ares-heading heading">Aide</h3>
			</div>
			<div class="body">
				<p>Afin de d&eacute;rouler les sections, cliquez sur leur titre.</p>
				<p>La section &quot;Sciences cognitives&quot; propose une br&egrave;ve explication des sciences cognitives et de leur utilit&eacute;</p>
				<p>La section &quot;Fonctionnement du site&quot; propose un court mode d&rsquo;emploi de ce site.</p>
				<p>La section &quot;Participation aux exp&eacute;rimentations&quot; propose aux chercheur(se)s, aux professeurs, aux sujets, aux &eacute;tudiant(e)s
				et aux curieux(ses) de cr&eacute;er et/ou participer &agrave; des exp&eacute;rimentations. Pour de plus amples d&eacute;tails sur les diff&eacute;rentes
				possibilit&eacute;s offertes aux diff&eacute;rentes cat&eacute;gories de personnes, consulter la section
				<a	class="ares-button link"
					onclick="javascript:section_clicked('fonctionnement_site'); this.parentNode.parentNode.parentNode.style.display = 'none';"
					href="#fonctionnement_site">Fonctionnement du site</a>.</p>
			</div>
		</div>
	</header>
	
	<article>
	<style type="text/css" media="screen">
		section > [id$="__content"]
		{
			height: 0;
			overflow: hidden;
		}
	</style>
	<script type="text/javascript">
		section_clicked = function(id)
		{
			switch (id)
			{
				case "sciences_cognitives":
					if (document.getElementById("sciences_cognitives__content").style.height == "auto")
					{document.getElementById("sciences_cognitives__content").style.height = "0";}
					else
					{
						document.getElementById("sciences_cognitives__content").style.height = "auto";
						document.getElementById("fonctionnement_site__content").style.height = "0";
						document.getElementById("participation__content").style.height = "0";
					}
					break;
				
				case "fonctionnement_site":
					if (document.getElementById("fonctionnement_site__content").style.height == "auto")
					{document.getElementById("fonctionnement_site__content").style.height = "0";}
					else
					{
						document.getElementById("fonctionnement_site__content").style.height = "auto";
						document.getElementById("sciences_cognitives__content").style.height = "0";
						document.getElementById("participation__content").style.height = "0";
					}
					break;
				
				case "participation":
					if (document.getElementById("participation__content").style.height == "auto")
					{document.getElementById("participation__content").style.height = "0";}
					else
					{
						document.getElementById("participation__content").style.height = "auto";
						document.getElementById("sciences_cognitives__content").style.height = "0";
						document.getElementById("fonctionnement_site__content").style.height = "0";
					}
					break;
			}
		}
	</script>
		<section style="margin-bottom: 2em;">
			<blockquote class="ares-text blockquote">
				&quot;La vie est une exp&eacute;rience. Plus on fait d&rsquo;exp&eacute;rience, mieux c&rsquo;est.&quot;
				<div class="source">Ralph Waldo <span style="font-variant: small-caps;">Emerson</span></div>
			</blockquote>
		</section>
		<section>
		<div id="sciences_cognitives" class="ares-navbar">
			<span class="brand" onclick="javascript:section_clicked('sciences_cognitives');">Sciences cognitives</span>
		</div>
			<div id="sciences_cognitives__content">
				<p class="ares-text">
					Les sciences cognitives regroupent un ensemble de disciplines scientifiques dédiées à la description, l'explication, et le cas échéant la simulation, des mécanismes de la pensée humaine, animale ou artificielle, et plus généralement de tout système complexe de traitement de l'information capable d'acquérir, conserver, utiliser et transmettre des connaissances. Les sciences cognitives reposent donc sur l'étude et la modélisation de phénomènes aussi divers que la perception, l'intelligence, le langage, le calcul, le raisonnement ou même la conscience. Les sciences cognitives utilisent conjointement des données issues d'une multitude de branches de la science et de l'ingénierie, comme la linguistique, l’anthropologie, la psychologie, les neurosciences, la philosophie, l'intelligence artificielle... Nées dans les années 1950, les sciences cognitives forment aujourd'hui un champ interdisciplinaire très vaste, dont les limites et le degré d'articulation des disciplines constitutives font toujours débat.
				</p>
				<p>
		En France, où la tradition disciplinaire est forte, la question de leur statut entre en résonance avec des problématiques liées à la structuration de la recherche. Divers regroupements de chercheurs, mais aussi d'étudiants, s'attachent à valoriser la pertinence et la portée de l'interdisciplinarité en sciences cognitives au travers de sociétés savantes comme l'Association pour la Recherche Cognitive (ARCo) ou d'associations comme la Fresco. Si certains contestent le statut des sciences cognitives comme discipline scientifique en tant que telle, d'autres estiment, au contraire, que les sciences cognitives ont dépassé le simple stade d'une accumulation de connaissances pluridisciplinaires et ont donné naissance à deux disciplines autonomes :
					<ul>
						<li>à une science fondamentale, dite science de la cognition, dont les spécialistes, parfois appelés cogniticiens, sont réunis en sociétés savantes et publient dans des revues scientifiques internationales transdisciplinaires ;</li>
						<li>à un secteur applicatif industriel du domaine de l'ingénierie de la connaissance : la cognitique.</li>
					</ul>
		Il est à noter que le singulier cognitive science est d'usage courant dans les pays anglophones.
				</p>
		</div>
		</section>
		
		<section>
		<div id="fonctionnement_site" class="ares-navbar">
			<span class="brand" onclick="javascript:section_clicked('fonctionnement_site');">Fonctionnement du site</span>
		</div>
		<div id="fonctionnement_site__content">
			<h5 class="ares-heading">1. Introduction</h5>
			<p class="ares-text">
				Ce site, intitul&eacute; &quot;Exp&eacute;rimentations de sciences cognitives en ligne&quot;, permet aux sujets des chercheur(se)s, aux &eacute;tudiant(e)s et aux curieux(ses) de participer &agrave; des exp&eacute;rimentation concernant les <a href="#sciences_cognitives" class="ares-button link">sciences cognitives</a>.
			</p>
			 	<h6 class="ares-heading">1.1. Chercheur(se)s et sujets d'exp&eacute;rience</h6>
				<p class="ares-text">
					Dans cette section, les chercheur(se)s proposent des exp&eacute;rimentations &agrave; des sujets afin d'en r&eacute;cup&eacute;rer les r&eacute;sultats plus tard.<br />
					Les sujets passent les exp&eacute;riences sans avoir acc&egrave;s aux r&eacute;sultats.
				</p>
				<h6 class="ares-heading">1.2. Enseignant(e)s et &eacute;tudiant(e)s</h6>
				<p>
					Dans cette section, les enseignant(e)s peuvent faire participer leurs &eacute;tudiant(e)s aux exp&eacute;rimentations en les regroupant par TP, par le biais d&rsquo;un identifiant TP.
					Enseignant(e)s comme &eacute;tudiant(e)s peuvent ensuite consulter les r&eacultes;sultats du groupe de TP uniquement.
				</p>
				<h6 class="ares-heading">1.3 Curieux(ses)</h6>
				<p>
					L&rsquo;utilisation de ce site n&rsquo;est pas restreinte &agrave; l&rsquo;enseignement et la recherche, toute personne curieuse peut participer aux exp&eacute;rimentations et acc&eacute;der &agrave; ses propres r&eacute;sultats uniquement.
				</p>
			<h5 class="ares-heading">2. Participation aux exp&eacute;rimentations</h5>
			<p>
				Chaque cat&eacute;gorie de particpant(e)s poss&egrave;de son propre acc&egrave;s aux exp&eacute;rimentations.
			</p>
				<h6 class="ares-heading">2.1. Recherche</h6>
				<p>
					Les chercheur(se)s poss&egrave;dent chacun(e) un identifiant et un mot de passe. Ces deux &eacute;l&eacute;ment leur permettront de se connecter en tant que chercheur(se) afin de r&eacute;cup&eacute;rer le r&eacute;sultats d&rsquo;exp&eacute;rimentation souhait&eacute;e. Le site propose &eacute;galement uen interface afin de cr&eacute;er et d&rsquo;ajouter une exp&eacute;rimentation.
				</p>
				<p>
					Les sujets sont des personnes invit&eacute;es par les chercheur(se)s &agrave; participer &agrave; certaines exp&eacute;rimentations. Lors de leur connexion, un identifiant leur est automatiquement attribu&eacute;. Cet identifiant, r&eacute;cup&eacute;rable &agrave; chaque instant, permettra au chercheur(se)s de consulter les r&eacute;sultats.
				</p>
				<h6 class="ares-heading">2.2. &Eacute;tudes</h6>
				<p>
					Les enseignant(e)s poss&egrave;de tous le m&ecirc;me mot de passe afin de se connecter. Cela leur permet de d&eacute;marrer une session de TP pendant laquelle plusieurs &eacute;tudiant(e)s participent aux m&ecirc;mes exp&eacute;rimentations. Lorsque tous les &eacute;tudiants ont effectu&eacute;s les tests, l&rsquo;enseignant(e) stoppe l&rsquo;exp&eacute;rimentation et peut ainsi r&eacute;cup&eacute;rer les r&eacute;sultats.
				</p>
				<p>
					Les &eacute;tudiant(e)s se connectent de la m&ecirc;me mani&egrave;re que les sujets des chercheur(se)s. En revanche, ils peuvent acc&eacute;der aux r&eacute;sultats de leur TP uniquement.
				</p>
				<h6 class="ares-heading">2.3. Curiosit&eacute;</h6>
				<p>
					Les curieux(ses) se connectent directement, sans pr&eacute;requis. Il leur est &eacute;galement possible de pr&eacute;ciser un identifiant pr&eacute;alablement obtenu afin de proc&eacute;der &agrave; un suivi des r&eacute;sultats.
				</p>
		</div>
		</section>
	
		<section>
		<div id="participation" class="ares-navbar">
			<span class="brand" onclick="javascript:section_clicked('participation');">Participation aux exp&eacute;rimentations</span>
		</div>
			<div id="participation__content">
				<p class="ares-text">
					Pour plus d&rsquo;informations sur la participation aux exp&eacute;rimentations, se r&eacute;f&eacute;rer à la section
					<span onclick="javascript:section_clicked('fonctionnement_site');" class="ares-button link">Fonctionnement du site</span>,
					paragraphe 2.{Participation aux exp&eacute;rimentations}.
				</p>
				<div id="recherche" class="ares-blockcontainer" style="margin-bottom: 0.5em">
					<span class="caption">Recherche</span>
					<p class="ares-text">
						Cette section est r&eacute;serv&eacute;e &agrave; la recherche.
					</p>
					<table class="ares-table no-outer-border no-header vertical-inner-border" style="text-align: left; margin: 0;">
						<tr>
							<td>
								Les scientifiques se connectent (ou s&rsquo;inscrivent) ici. Chacun(e) poss&egrave;de un identifiant et un mot de passe personnels.
							</td>
							<td>
								Les sujets se connectent ici. Si un sujet a d&eacute;j&agrave; obtenu un identifiant, il lui est n&eacute;cessaire de le pr&eacute;ciser dans le champ correspondant.
							</td>
						</tr>
						<tr>
							<td>
								<fieldset class="ares-fieldset">
								<legend>Connexion</legend>
									<form method="post" action="connexion/connexion_recherche.php" style="min-width: 18em;">
										<table>
											<tr>
												<td>
													<div class="ares-group vertical">
														<label for="recherche_identifiant" class="add-on">Identifiant</label>
														<input id="recherche_identifiant" type="text" name="recherche_identifiant"
															   class="ares-textinput <?php if (isset($_SESSION["modal_connexion"])) {echo("warning");} ?>" />
													</div>
												</td>
												<td>
													<div class="ares-group vertical">
														<label for="recherche_motdepasse" class="add-on">Mot de passe</label>
														<input id="recherche_motdepasse" type="password" name="recherche_motdepasse"
															   class="ares-textinput <?php if (isset($_SESSION["modal_connexion"])) {echo("failure");} ?>" />
													</div>
												</td>
											</tr>
											<tr>
												<td colspan="2" style="text-align: center;">
													<div>
														<input type="submit" class="ares-button" value="Connexion" />
													</div>
												</td>
											</tr>
										</table>
									</form>
								</fieldset>
								<fieldset class="ares-fieldset">
								<legend>Enregistrement</legend>
									<form method="post" action="connexion/enregistrement_recherche.php" style="min-width: 18em;">
										<table>
											<tr>
												<td>
													<div class="ares-group vertical">
														<label for="recherche_identifiant" class="add-on">Identifiant</label>
														<input id="recherche_identifiant" type="text" name="recherche_identifiant"
															   class="ares-textinput <?php if (isset($_SESSION["modal_enregistrement"])) {echo("warning");} ?>" required />
													</div>
												</td>
												<td>
													<div class="ares-group vertical">
														<label for="recherche_motdepasse" class="add-on">Mot de passe</label>
														<input id="recherche_motdepasse" type="password" name="recherche_motdepasse"
															   class="ares-textinput <?php if (isset($_SESSION["modal_enregistrement"])) {echo("failure");} ?>" required />
													</div>
												</td>
											</tr>
											<tr>
												<td colspan="2" style="text-align: center;">
													<div>
														<input type="submit" class="ares-button" value="Enregistrer" />
													</div>
												</td>
											</tr>
										</table>
									</form>
								</fieldset>
							</td>
							<td>
								<form method="post" action="pages_personnelles/sujet.php" style="min-width: 19em;">
									<table class="ares-table no-outer-border">
										<tr>
											<td>
												<div class="ares-group horizontal">
													<label for="recherche_idsujet" class="add-on">Identifiant sujet</label>
													<input id="recherche_idsujet" type="text" class="ares-textinput" name="recherche_idsujet"
														   placeholder="si d&eacute;j&agrave; poss&eacute;d&eacute;"
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<input type="submit" class="ares-button" value="Connexion" />
											</td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
				</div>
				
				<div id="etudes" class="ares-blockcontainer" style="margin-bottom: 0.5em">
					<span class="caption">Enseignement</span>
					<p class="ares-text">
						Cette section est r&eacute;serv&eacute;e &agrave; l&rsquo;enseignement.
					</p>
					<table class="ares-table no-outer-border no-header vertical-inner-border" style="text-align: left; margin: 0;">
						<tr>
							<td>
								Les enseignant(e)s se connectent ici. Un identifiant TP sera attribut&eacute; afin que l&rsquo;enseignant(e) puisse le communiquer &agrave; ses &eacute;tudiant(e)s.
							</td>
							<td style="width: 50%;">
								<p class="ares-text">
									Les &eacute;tudiant(e)s se connectent ici en pr&eacute;cisant l&rsquo;identifiant de leur TP. Si un(e) &eacute;tudiant(e) a déjà obtenu un identifiant, il lui est nécessaire de le préciser dans le champ correspondant.
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<form method="post" action="" style="min-width: 18em;">
									<table>
										<tr>
											<td>
												<div class="ares-group horizontal">
													<label for="enseignement_motdepasse" class="add-on">Mot de passe</label>
													<input id="enseignement_motdepasse" type="password" class="ares-textinput" />
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<input type="submit" class="ares-button" value="Connexion" />
											</td>
										</tr>
									</table>
								</form>
							</td>
							<td>
								<form method="post" action="" style="min-width: 18em;">
									<table>
										<tr>
											<td>
												<span class="ares-group vertical">
													<label for="etudes_identifiant" class="add-on">Identifiant</label>
													<input id="etudes_identifiant" type="text" class="ares-textinput" placeholder="facultatif" />
												</span>
											</td>
											<td>
												<span class="ares-group vertical">
													<label for="etudes_identifiant_tp" class="add-on">Identifiant TP</label>
													<input id="etudes_identifiant_tp" type="text" class="ares-textinput" />
												</span>
											</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align: center;">
												<input type="submit" class="ares-button" value="Connexion" />
											</td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
				</div>
				
				<div id="curiosite" class="ares-blockcontainer" style="margin-bottom: 0.5em">
					<span class="caption">Curiosit&eacute;</span>
					<p class="ares-text">
						Cette section est r&eacute;serv&eacute;e &agrave; la curiosit&eacute;. Toute personne voulant participer &agrave; une ou plusieurs exp&eacute;rimentations peut y parvenir simplement en appuyant sur le bouton &quot;Connexion&quot; ci dessous. Si un(e) curieux(se) poss&egrave;de d&eacute;j&agrave; un identifiant, il est possible de le pr&eacute;ciser afin d&rsquo;effectuer un suivi des r&eacute;sultats.
					</p>
					<form method="post" action="pages_personnelles/curieux.php" style="min-width: 18em;">
						<span class="ares-group horizontal">
							<label for="curosite_identifiant" class="add-on">Identifiant</label>
							<input id="curiosite_identifiant" type="text" class="ares-textinput" placeholder="facultatif" />
						</span>
						<br />
						<input type="submit" class="ares-button" value="Connexion" />
					</form>
				</div>
			</div>
		</section>
	</article>
</body>
 </html>
				
