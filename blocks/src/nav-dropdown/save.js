import { InnerBlocks } from '@wordpress/block-editor';

// Dynamic block: render.php builds the nav item + panel wrapper; save only
// persists the editable inner blocks (heading + any content the user adds).
export default function save() {
	return <InnerBlocks.Content />;
}
