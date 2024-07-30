'use client';

import Link from 'next/link';
import React from 'react';
import { usePathname } from 'next/navigation';
import { Container, Form } from 'react-bootstrap';
import classes from '@/styles/common.module.css';

const links = [
    {
        title: 'Library',
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
            <Container className={`${classes.header}`}>
                <nav>
                    <ul className="flex flex-wrap items-center gap-10">
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
                <Form.Control className={`bg-transparent py-3 px-6 text-white placeholder-neutral-400 border-neutral-600 rounded-lg w-auto`} type="text" placeholder="Search" />
            </Container>
        </>
    );
}
