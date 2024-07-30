import { getBookApi } from '@/app/api/book-api';
import { BookCreationDto, BookDto, BookPageDto } from '@/types/book-types';
import { useEffect, useState } from 'react';

export function useGetBooks(page: number) {
    const [books, setBooks] = useState<BookDto[]>([]);
    const [isLoading, setLoading] = useState(false);
    const [error, setError] = useState<Error | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                setLoading(true);
                const response = await getBookApi().get<BookPageDto>(
                    `/api/v1/books?sort_by=publication_time&sort_order=desc&page=${page}`,
                );
                setBooks(response.data.items);
                setError(null);
            } catch (err) {
                console.error(err);
                if (err instanceof Error) {
                    setError(err);
                }
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, [page]);

    return { books, isLoading, error };
}

export function useCreateBook() {
    const [isLoading, setLoading] = useState(false);
    const [error, setError] = useState<Error | null>(null);

    const sendData = async (bookCreationDto: BookCreationDto) => {
        try {
            setLoading(true);
            await getBookApi().post('/api/v1/books', bookCreationDto, {
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
            });
        } catch (err) {
            console.error(err);
            if (err instanceof Error) {
                setError(err);
            }
        } finally {
            setLoading(false);
        }
    };

    return { sendData, isLoading, error };
}
