const { TextControl } = wp.components

function siteTemplateInfo({ instanceId, label, children }) {
	return (
		<TextControl
			label='Foo'
			value='bar'
			onChange={ onChangeText }
		/>
	)
}
export default wp.compose.withInstanceId(siteTemplateInfo);
