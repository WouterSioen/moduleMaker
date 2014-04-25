{*
    variables that are available:
    - {$widget{$camel_case_name}Categories}:
*}

{option:widget{$camel_case_name}Categories}
    <section id="{$camel_case_name}CategoriesWidget" class="mod">
        <div class="inner">
            <header class="hd">
                <h3>{$lblCategories|ucfirst}</h3>
            </header>
            <div class="bd content">
                <ul>
                    {iteration:widget{$camel_case_name}Categories}
                        <li>
                            <a href="{$widget{$camel_case_name}Categories.url}">
                                {$widget{$camel_case_name}Categories.label}&nbsp;({$widget{$camel_case_name}Categories.total})
                            </a>
                        </li>
                    {/iteration:widget{$camel_case_name}Categories}
                </ul>
            </div>
        </div>
    </section>
{/option:widget{$camel_case_name}Categories}
