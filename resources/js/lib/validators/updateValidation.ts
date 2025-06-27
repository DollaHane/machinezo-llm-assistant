import { z } from 'zod';

export const updateValidation = z.object({
    title: z.string().min(3, { message: 'Must be at least 3 characters long' }).max(255, { message: 'Must be less than 255 characters long.' }),
    description: z
        .string()
        .min(3, { message: 'Must be at least 3 characters long' })
        .max(3000, { message: 'Must be less than 3,000 characters long.' }),
    plant_category: z
        .string()
        .min(3, { message: 'Must be at least 3 characters long' })
        .max(255, { message: 'Must be less than 255 characters long.' }),
    company_name: z
        .string()
        .min(3, { message: 'Must be at least 3 characters long' })
        .max(255, { message: 'Must be less than 255 characters long.' }),
    contact_email: z
        .string()
        .min(3, { message: 'Must be at least 3 characters long' })
        .max(255, { message: 'Must be less than 255 characters long.' }),
    phone_number: z
        .string()
        .min(3, { message: 'Must be at least 3 characters long' })
        .max(255, { message: 'Must be less than 255 characters long.' }),
    website: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    hire_rate_gbp: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    hire_rate_eur: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    hire_rate_usd: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    hire_rate_aud: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    hire_rate_nzd: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    hire_rate_zar: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    tags: z.array(
        z.string().min(3, { message: 'Must be at least 3 characters long' }).max(1000, { message: 'Must be less than 1,000 characters long.' }),
    ),
    company_logo: z.array(z.string().max(1000, { message: 'Must be less than 1,000 characters long.' }).optional()),
    photo_gallery: z.array(z.string().max(1000, { message: 'Must be less than 1,000 characters long.' }).optional()),
    attachments: z.array(z.string().max(1000, { message: 'Must be less than 1,000 characters long.' })).optional(),
    social_networks: z.array(z.string().max(1000, { message: 'Must be less than 1,000 characters long.' }).optional()),
    location: z.string().min(3, { message: 'Must be at least 3 characters long' }).max(255, { message: 'Must be less than 255 characters long.' }),
    latitude: z.coerce.number().optional(),
    longitude: z.coerce.number().optional(),
    region: z.string().min(3, { message: 'Must be at least 3 characters long' }).max(255, { message: 'Must be less than 255 characters long.' }),
    related_listing: z.array(z.string().max(1000, { message: 'Must be less than 1,000 characters long.' }).optional()),
    hire_rental: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_1: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_2: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_3: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_4: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_5: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_6: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_7: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_8: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_9: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
    additional_10: z.string().max(255, { message: 'Must be less than 255 characters long.' }).optional(),
});

export type UpdateValidationRequest = z.infer<typeof updateValidation>;
