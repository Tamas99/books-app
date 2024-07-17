import React from 'react';

async function getBooks() {
    const response = await fetch(
        'http://localhost:8000/api/v1/books?sort_by=publication_time&sort_order=desc&page=1&page_size=10',
    );

    if (!response.ok) {
        console.log(response);
        throw new Error('Something went wrong.');
    }

    return response.json();
}

export default async function Home() {
    const books = await getBooks();

    return (
        
        <h1>{books.items[0].title}</h1>
    );
}
