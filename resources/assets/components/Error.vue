<template>
    <div>
        <h1>{{ __('voyager::generic.error', { code: exception.status }) }}</h1>
        <p>{{ exception.message }}</p>
        <p>{{ exception.file }} at line {{ exception.line }}</p>
    </div>
</template>
<script>
export default {
    props: {
        exception: Object
    },
    /*
     * exception.exception can be one of:
     * - Voyager\Admin\Exceptions\NoLayoutFoundException
     *      => No layout was found for the given BREAD and action
     * - \Voyager\Admin\Exceptions\TableNotFoundException
     *      => The table used for editing a BREAD was not found
     */
}
</script>