/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Notice, ExternalLink } from '@wordpress/components';
import { createInterpolateElement } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Section from '.~/wcdl/section';
import AppButton from '.~/components/app-button';
import AccountCard, { APPEARANCE } from '.~/components/account-card';
import getWindowFeatures from '.~/utils/getWindowFeatures';
import useGoogleAdsAccount from '.~/hooks/useGoogleAdsAccount';
import AccountSwitch from './account-switch';
import ClaimPending from './claim-pending';

const ClaimAccount = () => {
	const {
		googleAdsAccountStatus: { inviteLink },
	} = useGoogleAdsAccount();

	const handleClick = ( e ) => {
		const { defaultView } = e.target.ownerDocument;
		const features = getWindowFeatures( defaultView, 600, 800 );

		defaultView.open( inviteLink, '_blank', features );
	};

	return (
		<AccountCard
			appearance={ APPEARANCE.GOOGLE_ADS }
			alignIcon="top"
			indicator={
				<AppButton isSecondary onClick={ handleClick }>
					{ __( 'Claim', 'google-listings-and-ads' ) }
				</AppButton>
			}
		>
			<Section.Card.Body>
				<Notice status="warning" isDismissible={ false }>
					{ createInterpolateElement(
						__(
							'Your new ads account has been created, but you do not have access to it yet. <link>Claim your new ads account</link> to automatically configure conversion tracking and configure onboarding',
							'google-listings-and-ads'
						),
						{ link: <ExternalLink href={ inviteLink } /> }
					) }
				</Notice>
			</Section.Card.Body>

			<Section.Card.Footer>
				<AccountSwitch />
			</Section.Card.Footer>

			<ClaimPending />
		</AccountCard>
	);
};

export default ClaimAccount;
