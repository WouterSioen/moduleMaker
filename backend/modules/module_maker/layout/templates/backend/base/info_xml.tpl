<?xml version="1.0" encoding="UTF-8"?>
<module>
	<name>{$module.title}</name>
	<version>1.0.0</version>
	<requirements>
		<minimum_version>versionname</minimum_version>
	</requirements>
	<description>
		<![CDATA[
			{$module.description}
		]]>
	</description>
	<authors>
		<author>
			<name><![CDATA[{$module.author_name}]]></name>
			<url><![CDATA[{$module.author_url}]]></url>
		</author>
	</authors>
	<events>
		<event application="backend" name="after_add"><![CDATA[Triggered when a {$module.title} is added.]]></event>
		<event application="backend" name="after_delete"><![CDATA[Triggered when a {$module.title} is deleted.]]></event>
		<event application="backend" name="after_edit"><![CDATA[Triggered when a {$module.title} is edited.]]></event>
		<event application="backend" name="after_saved_settings"><![CDATA[Triggered when settings are saved.]]></event>
	</events>
</module>