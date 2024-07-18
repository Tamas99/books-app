import { getBooks } from '@/service/book-service';
import React, { useEffect, useState } from 'react';
import Book from './Book';
import { Container } from 'react-bootstrap';
import { BookDto, BookPageDto } from '@/types/book-types';

export default function BookList() {
    const [bookList, setBookList] = useState(Array<BookDto>());

    useEffect(() => {
        const callGetBooks = async () => {
            const books: BookPageDto = await getBooks();
            setBookList(books.items);
        };

        callGetBooks();
    });

    return (
        <>
            {bookList.length === 0 ? (
                <p className="text-neutral-600">No books found</p>
            ) : (
                <Container className="grid gap-4 p-5 md:grid-cols-2 lg:grid-cols-3">
                    {bookList.map((book: BookDto) => (
                        <Book key={book.id} book={book} />
                    ))}
                </Container>
            )}
        </>
    );
}
