{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lbl{$camel_case_name}|ucfirst}: {$msgEditCategory|sprintf:{$item.title}}</h2>
</div>

{form:editCategory}
    <div class="tabs">
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
            <li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
        </ul>

        <div id="tabContent">
            <div class="box">
                <div class="heading">
                    <h3><label for="title">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></h3>
                </div>
                <div class="options">
                    {$txtTitle} {$txtTitleError}
                </div>
            </div>
        </div>

        <div id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
        </div>
    </div>

    <div class="fullwidthOptions">
        <a href="{$var|geturl:'delete_category'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
            <span>{$lblDelete|ucfirst}</span>
        </a>
        <div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
            <p>
                {$msgConfirmDeleteCategory|sprintf:{$item.title}}
            </p>
        </div>

        <div class="buttonHolderRight">
            <input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
        </div>
    </div>
{/form:editCategory}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
