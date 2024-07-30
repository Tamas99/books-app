import { z } from 'zod';

export const createBookFormSchema = z.object({
    title: z
        .string()
        .min(1)
        .max(50),
    author: z
        .string()
        .min(2)
        .max(50),
    isbn: z
        .string()
        .length(13),
    creation_date: z
        .string()
        .date(),
    description: z
        .string()
        .min(5)
        .max(200),
});

export type CreateBookFormType = z.infer<typeof createBookFormSchema>;
