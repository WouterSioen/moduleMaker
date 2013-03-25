{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleMaker|ucfirst}: {$lblWizardBlocks|ucfirst}</h2>
</div>

<div class="wizard">
	<ul>
		<li><a href="{$var|geturl:'add'}"><b><span>1.</span> {$lblWizardInformation|ucfirst}</b></a></li>
		<li><a href="{$var|geturl:'add_step2'}"><b><span>2.</span> {$lblWizardFields|ucfirst}</b></a></li>
		<li class="beforeSelected"><a href="{$var|geturl:'add_step3'}"><b><span>3.</span> {$lblWizardSpecialFields|ucfirst}</b></a></li>
		<li class="selected"><a href="{$var|geturl:'add_step4'}"><b><span>4.</span> {$lblWizardBlocks|ucfirst}</b></a></li>
		<li><b><span>5.</span> {$lblWizardGenerate|ucfirst}</b></li>
	</ul>
</div>

<div class="box">
	<div class="heading">
		<h3>TO DO</h3>
	</div>
	<div class="options">
		<pre>
Hier komt de mogelijkheid om frontend blocks en widgets toe te voegen

Blocks: 
	- Index (sowieso aanwezig)
		* ev. aanduiden op welk veld gesorteerd moet worden
		* ev. aanduiden of er paginatie wordt gebruikt
	- Detail (sowieso aanwezig als meta is aangevink)
		* ev. aanduiden welke velden moeten getoond worden
Widgets: mogelijkheid om parameters mee te geven
	- Veld die een bepaalde waarde moet hebben
	- Sortering op een bepaald veld
	- ...
		</pre>
	</div>
	<div class="options">
		{$item|dump}
	</div>
</div>

<div class="fullwidthOptions">
	<div class="buttonHolderRight">
		<a id="generate" class="inputButton button mainButton" href="{$var|geturl:'generate'}" >{$lblGenerate|ucfirst}</a>
	</div>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}