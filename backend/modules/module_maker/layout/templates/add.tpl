{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleMaker|ucfirst}: {$lblWizardInformation|ucfirst}</h2>
</div>

<div class="wizard">
	<ul>
		<li class="selected firstChild"><a href="{$var|geturl:'add'}"><b><span>1.</span> {$lblWizardInformation|ucfirst}</b></a></li>
		<li><b><span>2.</span> {$lblWizardFields|ucfirst}</b></li>
		<li><b><span>3.</span> {$lblWizardSpecialFields|ucfirst}</b></li>
		<li><b><span>4.</span> {$lblWizardBlocks|ucfirst}</b></li>
		<li><b><span>5.</span> {$lblWizardGenerate|ucfirst}</b></li>
	</ul>
</div>

{form:add}
	<p>
		<label for="title">{$lblTitle|ucfirst}</label>
		{$txtTitle} {$txtTitleError}
	</p>

	{* Main content *}
	<div class="box">
		<div class="heading">
			<h3>{$lblDescription|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></h3>
		</div>
		<div class="options">
			{$txtDescription} {$txtDescriptionError}
		</div>
	</div>

	<div id="publishOptions" class="box">
		<div class="heading">
			<h3>{$lblAuthor|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="author_name">{$lblAuthorName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtAuthorName} {$txtAuthorNameError}
			</p>
			<p>
				<label for="author_url">{$lblAuthorUrl|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtAuthorUrl} {$txtAuthorUrlError}
			</p>
			<p>
				<label for="author_email">{$lblAuthorEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtAuthorEmail} {$txtAuthorEmailError}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="toStep2" class="inputButton button mainButton" type="submit" name="to_step_2" value="{$lblToStep|ucfirst} 2" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}