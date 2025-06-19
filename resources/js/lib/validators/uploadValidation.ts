import { z } from 'zod';

export const uploadValidation = z.array(
    z.object({
        title: z.string(),
        description: z.string(),
        plantCategory: z.string(),
        contactEmail: z.string(),
        phoneNumber: z.string(),
        website: z.string(),
        hireRatePricing: z.string(),
        tags: z.string(),
        companyLogo: z.string(),
        photoGallery: z.string(),
        attachments: z.string(),
        socialNetworks: z.string(),
        location: z.string(),
        region: z.string(),
        relatedListing: z.string(),
        hireRental: z.string(),
    }),
);

export const backendValidation = z.object({
    files: z.instanceof(File).optional(),
});

export type UploadValidationRequest = z.infer<typeof uploadValidation>;
export type BackendValidationRequest = z.infer<typeof backendValidation>;
