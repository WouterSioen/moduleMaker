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

{form:add_step3}
	<div id="options" class="box">
		<div class="heading">
			<h3>{$lblOptions|ucfirst}</h3>
		</div>
		<div class="options horizontal">
			<p>
				<label for="meta">{$chkMeta} {$lblMeta|ucfirst}</label>
				<span class="showOnMeta"{option:!meta} style="display: none;"{/option:!meta}>
					<label for="field">{$lblField|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$ddmField} {$ddmFieldError}
				</span>
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addStep3" class="inputButton button mainButton" type="submit" name="add_step_3" value="{$lblToStep|ucfirst} 4" />
		</div>
	</div>
{/form:add_step3}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}