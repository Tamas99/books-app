import { BookDto } from '@/types/book-types';
import React from 'react';
import { Container, Form } from 'react-bootstrap';

interface BookProp {
    book: BookDto;
}

export default function Book({ book }: BookProp) {
    return (
        <Container key={book.id} className="space-y-4 rounded-lg border border-neutral-600 p-4">
            <Form.Label className="font-bold text-lg">{book.title}</Form.Label>
            <Form.Label className="text-sm text-neutral-400">
                <strong>Author:</strong> {book.author}
            </Form.Label>
            <Form.Label className="text-sm text-neutral-400">
                <strong>ISBN:</strong> {book.isbn}
            </Form.Label>
        </Container>
    );
}
