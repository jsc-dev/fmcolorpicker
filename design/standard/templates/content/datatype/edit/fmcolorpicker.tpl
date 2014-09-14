{default attribute_base='ContentObjectAttribute' html_class='full'}
<input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}" class="{eq( $html_class, 'half' )|choose( 'box', 'halfbox' )} ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier} cp{$attribute.id} color" type="color" size="11" maxlength="11" name="{$attribute_base}_fmcolorpicker_data_text_{$attribute.id}" value="{$attribute.data_text|wash( xhtml )}" data-hex="true" style="width:100px;" />
{/default}

<script type="text/javascript" src={"javascript/mColorPicker/javascripts/mColorPicker.js"|ezdesign}></script>
{literal}
<script type="text/javascript">
$(document).ready(function() {
	$('#ezcoa-{/literal}{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}{literal}').bind('colorpicked', function () {
	});
});
</script>
{/literal}

