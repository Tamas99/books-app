'use client';

import Link from 'next/link';
import React from 'react';
import { usePathname } from 'next/navigation';

const links = [
    {
        title: 'Library',
        url: '/',
    },
    {
        title: 'Book List',
        url: '/',
    },
    {
        title: 'Create Book',
        url: '/create-book',
    },
];

export default function Header() {
    const pathname = usePathname();

    return (
        <>
            <header className="flex flex-wrap items-center justify-between gap-4 border p-6 ml-40 mr-40">
                <nav>
                    <ul className="flex flex-wrap items-center justify-center gap-10">
                        {links.map((link) => (
                            <li key={link.url}>
                                <Link
                                    href={link.url}
                                    className={`${
                                        pathname === link.url ? 'font-bold opacity-100' : 'font-normal opacity-75'
                                    } transition`}
                                >
                                    {link.title}
                                </Link>
                            </li>
                        ))}
                    </ul>
                </nav>
                <p className="position-right">Search</p>
            </header>
        </>
    );
}
