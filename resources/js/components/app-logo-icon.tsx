import Logo from '@/assets/Machinezo_Logo.png';
import { SVGAttributes } from 'react';
export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <div className="w-10">
            <img src={Logo} alt="machinezo-logo" className="size-full" />
        </div>
    );
}
