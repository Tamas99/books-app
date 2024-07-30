'use client';

import { useGetBooks } from '@/service/book-service';
import React, { useState } from 'react';
import Book from './Book';
import { Container, Spinner } from 'react-bootstrap';
import { BookDto } from '@/types/book-types';
import classes from '@/styles/common.module.css';

export default function BookList() {
    const [page, setPage] = useState(1);
    const { books, isLoading, error } = useGetBooks(page);

    return (
        <>
            {isLoading ? (
                <div className={`${classes.loadingSpinner}`}>
                    <Spinner />
                </div>
            ) : error ? (
                <Container className="my-10 p-10">
                    <p className="py-10 text-center font-bold">Something went wrong</p>
                </Container>
            ) : books!.length === 0 ? (
                <Container className="my-10 p-10">
                    <p className="py-10 text-center font-bold">No books found</p>
                </Container>
            ) : (
                <Container className="grid gap-4 p-5 md:grid-cols-2 lg:grid-cols-3">
                    {books!.map((book: BookDto) => (
                        <Book key={book.id} book={book} />
                    ))}
                </Container>
            )}
        </>
    );
}
