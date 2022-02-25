/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { createInterpolateElement, useState } from '@wordpress/element';
import { Form } from '@woocommerce/components';
import { getHistory } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */
import StepContent from '.~/components/stepper/step-content';
import StepContentHeader from '.~/components/stepper/step-content-header';
import StepContentFooter from '.~/components/stepper/step-content-footer';
import AppDocumentationLink from '.~/components/app-documentation-link';
import AppButton from '.~/components/app-button';
import useDispatchCoreNotices from '.~/hooks/useDispatchCoreNotices';
import { useAppDispatch } from '.~/data';
import CreateCampaignFormContent from '.~/components/paid-ads/create-campaign-form-content';
import validateForm from '.~/utils/paid-ads/validateForm';
import { getDashboardUrl } from '.~/utils/urls';
import { recordLaunchPaidCampaignClickEvent } from '.~/utils/recordEvent';

const CreatePaidAdsCampaignForm = () => {
	const [ loading, setLoading ] = useState( false );
	const { createAdsCampaign } = useAppDispatch();
	const { createNotice } = useDispatchCoreNotices();

	const handleValidate = ( values ) => {
		return validateForm( values );
	};

	const handleSubmit = async ( values ) => {
		setLoading( true );

		try {
			const { amount, countryCodes } = values;
			const country = countryCodes[ 0 ];

			recordLaunchPaidCampaignClickEvent( amount, country );

			await createAdsCampaign( amount, country );

			createNotice(
				'success',
				__(
					'You’ve successfully created a paid campaign!',
					'google-listings-and-ads'
				)
			);
		} catch ( e ) {
			setLoading( false );
			return;
		}

		getHistory().push( getDashboardUrl() );
	};

	return (
		<Form
			initialValues={ {
				amount: 0,
				countryCodes: [],
			} }
			validate={ handleValidate }
			onSubmit={ handleSubmit }
		>
			{ ( formProps ) => {
				const {
					isValidForm,
					handleSubmit: handleLaunchCampaignClick,
				} = formProps;

				return (
					<StepContent>
						<StepContentHeader
							title={ __(
								'Create your paid campaign',
								'google-listings-and-ads'
							) }
							description={ createInterpolateElement(
								__(
									'Paid Smart Shopping campaigns are automatically optimized for you by Google. <link>See what your ads will look like.</link>',
									'google-listings-and-ads'
								),
								{
									link: (
										<AppDocumentationLink
											context="create-ads"
											linkId="see-what-ads-look-like"
											href="https://support.google.com/google-ads/answer/6275294"
										/>
									),
								}
							) }
						/>
						<CreateCampaignFormContent formProps={ formProps } />
						<StepContentFooter>
							<AppButton
								isPrimary
								disabled={ ! isValidForm }
								loading={ loading }
								onClick={ handleLaunchCampaignClick }
							>
								{ __(
									'Launch paid campaign',
									'google-listings-and-ads'
								) }
							</AppButton>
						</StepContentFooter>
					</StepContent>
				);
			} }
		</Form>
	);
};

export default CreatePaidAdsCampaignForm;
