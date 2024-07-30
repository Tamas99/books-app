import 'bootstrap/dist/css/bootstrap.css';

import type { Metadata } from 'next';
import './globals.css';
import Header from '@/components/Header'

export const metadata: Metadata = {
    title: 'Books',
};

interface RootLayoutProps {
    children: React.ReactNode;
}

export default function RootLayout({ children }: RootLayoutProps) {
    return (
        <html lang="en">
            <body>
                <Header />
                {children}
            </body>
        </html>
    );
}
