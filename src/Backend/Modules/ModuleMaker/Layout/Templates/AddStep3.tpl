{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

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

{form:add_step3}
    <div id="options" class="box">
        <div class="heading">
            <h3>{$lblOptions|ucfirst}</h3>
        </div>
        <div class="options horizontal">
            <p>
                <label for="meta_field">{$lblMetaField|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                {$ddmMetaField} {$ddmMetaFieldError}
            </p>
        </div>
        <div class="options horizontal">
            <p>
                <label for="search">{$chkSearch} {$lblSearch|ucfirst}</label>
            </p>
            <span class="showOnSearch"{option:!search} style="display: none;"{/option:!search}>
                {option:searchFields}
                    <p>
                        <label>{$lblSearchFields|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                    </p>
                    <ul id="searchFieldsList" class="inputList">
                        {iteration:searchFields}
                            <li>{$searchFields.chkSearchFields} <label for="{$searchFields.id}">{$searchFields.label}</label></li>
                        {/iteration:searchFields}
                    </ul>
                    {$chkSearchFieldsError}
                {/option:searchFields}
            </span>
        </div>
        <div class="options">
            <p>
                <label for="tags">{$chkTags} {$lblTags|ucfirst}</label>
            </p>
        </div>
        <div class="options">
            <p>
                <label for="sequence">{$chkSequence} {$lblSequence|ucfirst}</label>
            </p>
        </div>
        <div class="options">
            <p>
                <label for="categories">{$chkCategories} {$lblCategories|ucfirst}</label>
            </p>
        </div>
        <div class="options">
            <p>
                <label for="multipleImages">{$chkMultipleImages} {$lblMultipleImages|ucfirst}</label>
            </p>
        </div>
    </div>

    <div class="fullwidthOptions">
        <div class="buttonHolder">
            <a id="toStep2" class="inputButton button" href="{$var|geturl:'add_step2'}" >{$lblBack|ucfirst}</a>
        </div>
        <div class="buttonHolderRight">
            <input id="addStep4" class="inputButton button mainButton" type="submit" name="add_step_4" value="{$lblToStep|ucfirst} 4" />
        </div>
    </div>
{/form:add_step3}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
