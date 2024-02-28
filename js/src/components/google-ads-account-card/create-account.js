/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Section from '.~/wcdl/section';
import AppButton from '.~/components/app-button';
import AccountCard, { APPEARANCE } from '.~/components/account-card';
import CreateAccountButton from './create-account-button';
import useApiFetchCallback from '.~/hooks/useApiFetchCallback';
import { useAppDispatch } from '.~/data';
import useDispatchCoreNotices from '.~/hooks/useDispatchCoreNotices';

const ClaimTermsAndCreateAccountButton = () => {
	const { createNotice } = useDispatchCoreNotices();
	const { fetchGoogleAdsAccount } = useAppDispatch();
	const [ fetchAccountLoading, setFetchAccountLoading ] = useState( false );
	const [ fetchCreateAdsAccount, { loading: createLoading } ] =
		useApiFetchCallback( {
			path: `/wc/gla/ads/accounts`,
			method: 'POST',
		} );

	const handleCreateAccount = async () => {
		try {
			await fetchCreateAdsAccount( { parse: false } );
		} catch ( e ) {
			// for status code 428, we want to allow users to continue and proceed,
			// so we swallow the error for status code 428,
			// and only display error message and exit this function for non-428 error.
			if ( e.status !== 428 ) {
				createNotice(
					'error',
					__(
						'Unable to create Google Ads account. Please try again later.',
						'google-listings-and-ads'
					)
				);
				return;
			}
		}

		setFetchAccountLoading( true );
		await fetchGoogleAdsAccount();
		setFetchAccountLoading( false );
	};

	return (
		<CreateAccountButton
			loading={ createLoading || fetchAccountLoading }
			onCreateAccount={ handleCreateAccount }
		/>
	);
};

const CreateAccount = ( props ) => {
	const { allowShowExisting, onShowExisting, disabled } = props;

	return (
		<AccountCard
			disabled={ disabled }
			appearance={ APPEARANCE.GOOGLE_ADS }
			alignIcon="top"
			indicator={
				disabled ? null : (
					<ClaimTermsAndCreateAccountButton disabled={ disabled } />
				)
			}
		>
			{ allowShowExisting && (
				<Section.Card.Footer>
					<AppButton
						isLink
						onClick={ onShowExisting }
						disabled={ disabled }
					>
						{ __(
							'Or, use your existing Google Ads account',
							'google-listings-and-ads'
						) }
					</AppButton>
				</Section.Card.Footer>
			) }
		</AccountCard>
	);
};

export default CreateAccount;
