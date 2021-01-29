import wretch from 'wretch';
import { WretcherOptions } from 'wretch';

export default (url?: string, options?: WretcherOptions): any => {
    let token: string = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '';
    
    return wretch(url, options).headers({ 'X-CSRF-TOKEN': token });
}