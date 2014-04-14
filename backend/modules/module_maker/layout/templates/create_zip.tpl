{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblCreateZip|ucfirst}</h2>
</div>

{option:dataGridInstalledModules}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>{$lblInstalledModules|ucfirst}</h3>
	</div>
	{$dataGridInstalledModules}
</div>
{/option:dataGridInstalledModules}
{option:!dataGridInstalledModules}<p>{$msgNoModulesInstalled}</p>{/option:!dataGridInstalledModules}

{option:dataGridInstallableModules}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblInstallableModules|ucfirst}</h3>
		</div>
		{$dataGridInstallableModules}
	</div>
{/option:dataGridInstallableModules}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
