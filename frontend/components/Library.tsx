import React from 'react';
import BookList from './BookList';
import { Container } from 'react-bootstrap';

export default function Library() {
    return (
        <>
            <Container className="mx-auto max-w-6xl">
                <h2 className="mb-8 text-center text-4xl font-bold">Books</h2>
                <BookList />
            </Container>
        </>
    );
}
