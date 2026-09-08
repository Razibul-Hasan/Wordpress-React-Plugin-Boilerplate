import { classNames } from '../utils/Utils';

/**
 * Shared button. Extend the variant list rather than styling buttons inline.
 */
const Button = ( {
	children,
	onClick,
	variant = 'primary',
	disabled = false,
	type = 'button',
} ) => (
	<button
		type={ type }
		className={ classNames( 'wpb-btn', `wpb-btn--${ variant }` ) }
		onClick={ onClick }
		disabled={ disabled }
	>
		{ children }
	</button>
);

export default Button;
