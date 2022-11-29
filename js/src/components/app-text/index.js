/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import './index.scss';

/**
 * Renders a paragraph with some predefined styles based on the variant
 *
 *
 * ```jsx
 * <AppText variant="title-small">
 * 		My text
 * </AppText>
 * ```
 *
 * @param {Object} props Component props
 * @param {string} [props.variant] The variant to use for the text
 * @param {string} [props.className] Custom classname for this component
 * @param {keyof JSX.IntrinsicElements} [props.as] as Custom HTML tag for the component (by default <p>)
 * @param {JSX.Element} props.children The content for this component
 * @param {Object} [props.rest] Params to be forworded to the component
 * @return {JSX.Element} The component
 */
const AppText = ( {
	variant,
	className = '',
	children,
	as = 'p',
	...rest
} ) => {
	const AsTag = as;
	return (
		<AsTag
			{ ...rest }
			className={ classnames( 'gla-app-text', className, {
				[ `gla-app-text--${ variant }` ]: variant,
			} ) }
		>
			{ children }
		</AsTag>
	);
};

export default AppText;
