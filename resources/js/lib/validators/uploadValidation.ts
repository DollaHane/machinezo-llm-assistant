import { z } from 'zod';

export const uploadValidation = z.array(
    z.object({
        title: z.string(),
        description: z.string(),
        plant_category: z.string(),
        contact_email: z.string(),
        phone_number: z.string(),
        website: z.string().optional(),
        hire_rate_gbp: z.string().optional(),
        hire_rate_eur: z.string().optional(),
        hire_rate_usd: z.string().optional(),
        hire_rate_aud: z.string().optional(),
        hire_rate_nzd: z.string().optional(),
        hire_rate_zar: z.string().optional(),
        tags: z.array(z.string()),
        company_logo: z.array(z.string()).optional(),
        photo_gallery: z.array(z.string()).optional(),
        attachments: z.string().optional(),
        social_networks: z.array(z.string()).optional(),
        location: z.string(),
        region: z.string(),
        related_listing: z.array(z.string()).optional(),
        hire_rental: z.string().optional(),
        additional_1: z.string().optional(),
        additional_2: z.string().optional(),
        additional_3: z.string().optional(),
        additional_4: z.string().optional(),
        additional_5: z.string().optional(),
        additional_6: z.string().optional(),
        additional_7: z.string().optional(),
        additional_8: z.string().optional(),
        additional_9: z.string().optional(),
        additional_10: z.string().optional(),
    }),
);

export type UploadValidationRequest = z.infer<typeof uploadValidation>;
