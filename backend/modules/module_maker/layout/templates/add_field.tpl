{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleMaker|ucfirst}: {$lblAddField|ucfirst}</h2>
</div>

{form:add_field}
	<p>
		<label for="label">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
		{$txtLabel} {$txtLabelError}
	</p>

	{* Main content *}
	<div id="options" class="box">
		<div class="heading">
			<h3>{$lblOptions|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="type">{$lblType|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$ddmType} {$ddmTypeError}
			</p>
			<p id="jsToggleOptions" style="display:none;">
				<label for="tags">{$lblOptions|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtTags} {$txtTagsError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="required">{$chkRequired} {$lblRequired|ucfirst}</label>
			</p>
			<p>
				<label for="default">{$lblDefault|ucfirst}</label>
				{$txtDefault} {$txtDefaultError}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addField" class="inputButton button mainButton" type="submit" name="add_field" value="{$lblPublish|ucfirst}" />
		</div>
	</div>
{/form:add_field}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}