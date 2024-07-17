'use client';

import React, { useState } from 'react';
import { toast, ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { format } from 'date-fns';

export default function NewBook() {
    const [bookTitle, setBookTitle] = useState('');
    const [bookAuthor, setBookAuthor] = useState('');
    const [bookISBN, setBookISBN] = useState('');
    const [bookCreatedDate, setBookDate] = useState('');
    const [bookDescription, setBookDescription] = useState('');

    async function handleSubmit(e) {
        e.preventDefault();

        try {
            if (!bookTitle) {
                toast.error('Book title is required');
            } else if (!bookAuthor) {
                toast.error('Book author is required');
            } else if (!bookISBN) {
                toast.error('Book isbn is required');
            } else if (!bookCreatedDate) {
                toast.error('Book date is required');
            } else if (!bookDescription) {
                toast.error('Book description is required');
            } else {
                const newBook = {
                    title: bookTitle,
                    author: bookAuthor,
                    isbn: bookISBN,
                    createdDate: format(new Date(bookCreatedDate), 'yyyy-MM-dd HH:mm:ss'),
                    description: bookDescription,
                };

                await fetch('http://localhost:8000/api/v1/books', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(newBook),
                });

                setBookTitle('');
                setBookDate('');
                setBookDescription('');
                setBookAuthor('');
                setBookISBN('');

                toast.success('New book added!');
            }
        } catch (error) {
            toast.error('An error occurred in a component');
        }
    }

    return (
        <>
            <ToastContainer theme="colored" autoClose={2000} />

            <section className="flex items-center justify-center px-6 py-10 lg:py-20">
                <form onSubmit={handleSubmit} className="space-y-8">
                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <label htmlFor="book-title">Book Title</label>
                            <input
                                type="text"
                                name="book-title"
                                id="book-title"
                                required
                                placeholder="What is the title of the book?"
                                value={bookTitle}
                                onChange={(e) => setBookTitle(e.target.value)}
                            />
                        </div>

                        <div>
                            <label htmlFor="book-author">Book Author</label>
                            <input
                                type="text"
                                name="book-author"
                                id="book-author"
                                required
                                placeholder="What is the author of the book?"
                                value={bookAuthor}
                                onChange={(e) => setBookAuthor(e.target.value)}
                            />
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <label htmlFor="book-date">Book Creation Date</label>
                            <input
                                type="date"
                                name="book-date"
                                id="book-date"
                                required
                                placeholder="What is the date of the book?"
                                value={bookCreatedDate}
                                onChange={(e) => setBookDate(e.target.value)}
                            />
                        </div>

                        <div>
                            <label htmlFor="book-isbn">Book ISBN</label>
                            <input
                                type="text"
                                name="book-isbn"
                                id="book-isbn"
                                required
                                placeholder="Who is the primary isbn of the book?"
                                value={bookISBN}
                                onChange={(e) => setBookISBN(e.target.value)}
                            />
                        </div>
                    </div>

                    <div>
                        <label htmlFor="book-description">Book Description</label>
                        <textarea
                            name="book-description"
                            id="book-description"
                            cols="30"
                            rows="6"
                            required
                            placeholder="Give a short description about the book"
                            value={bookDescription}
                            onChange={(e) => setBookDescription(e.target.value)}
                        ></textarea>
                    </div>

                    <button
                        onClick={handleSubmit}
                        type="submit"
                        className="w-full rounded-lg bg-white px-6 py-3 font-semibold text-neutral-900 outline-none hover:animate-pulse"
                    >
                        Create new book
                    </button>
                </form>
            </section>
        </>
    );
}
