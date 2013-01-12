{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleMaker|ucfirst}: {$lblWizardSpecialFields|ucfirst}</h2>
</div>

<div class="wizard">
	<ul>
		<li><a href="{$var|geturl:'add'}"><b><span>1.</span> {$lblWizardInformation|ucfirst}</b></a></li>
		<li class="beforeSelected"><a href="{$var|geturl:'add_step2'}"><b><span>2.</span> {$lblWizardFields|ucfirst}</b></a></li>
		<li class="selected"><a href="{$var|geturl:'add_step3'}"><b><span>3.</span> {$lblWizardSpecialFields|ucfirst}</b></a></li>
		<li><b><span>4.</span> {$lblWizardBlocks|ucfirst}</b></li>
		<li><b><span>5.</span> {$lblWizardGenerate|ucfirst}</b></li>
	</ul>
</div>

<div class="box">
	<div class="heading">
		<h3>TO DO</h3>
	</div>
	<div class="options">
		<pre>
Hier komen speciale velden zoals
- meta
- categorieën
- extra afbeeldingen tabel
- extra settings tabel (key => value)
- Tags
- Search Index
- Sequence
		</pre>
	</div>
</div>

<div class="fullwidthOptions">
	<div class="buttonHolderRight">
		<a id="toStep4" class="inputButton button mainButton" href="{$var|geturl:'add_step4'}" >{$lblToStep|ucfirst} 4</a>
	</div>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}