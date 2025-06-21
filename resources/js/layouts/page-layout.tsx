import React from 'react';

export default function PageLayout({ children, ...props }: React.ComponentProps<'div'>) {
    return (
        <div {...props} className="flex w-full flex-col justify-center p-10">
            {children}
        </div>
    );
}
