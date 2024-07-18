import React from 'react';
import { ToastContainer } from 'react-toastify';
import BookList from './BookList';
import { Container } from 'react-bootstrap';

export default function Library() {
    return (
        <>
            <ToastContainer theme="colored" autoClose={2000} />

            <Container className="mx-auto max-w-6xl px-6 py-10">
                <h2 className="mb-8 text-center text-4xl font-bold">Books</h2>
                <BookList />
            </Container>
        </>
    );
}
