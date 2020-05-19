@extends('voyager::app')

@section('page-title', 'UI')

@section('content')
<card title="UI Elements">
    <div>
        <div class="inline w-full">
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-headings')">
                Headings
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-buttons')">
                Buttons
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-inputs')">
                Inputs
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-select')">
                Select
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-tags')">
                Tag input
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-wysiwyg')">
                WYSIWYG Editor
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-tabs')">
                Tabs
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-badges')">
                Badges
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-alerts')">
                Alerts
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-notifications')">
                Notifications
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-scrollbars')">
                Scrollbars
            </button>
            <button class="button blue" @click="$refs.scroll.scrollToElementID('#ui-pagination')">
                Pagination
            </button>
        </div>
    </div>
</card>

<collapsible title="Heading" id="ui-headings">
    <h1>H1 Heading</h1>
    <h2>H2 Heading</h2>
    <h3>H3 Heading</h3>
    <h4>H4 Heading</h4>
    <h5>H5 Heading</h5>
    <h6>H6 Heading</h6>
</collapsible>

<collapsible title="Buttons" id="ui-buttons">
    <collapsible title="Default">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', color]">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Disabled">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', color]" disabled>
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Small">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'small', color]">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Large">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'large', color]">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="With Icon">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'small', color]">
            <icon icon="info-circle" class="mr-1"></icon>
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Responsive">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'small', color]">
            <icon icon="info-circle"></icon>
            <span>@{{ color[0].toUpperCase() + color.slice(1) }}</span>
        </button>
    </collapsible>
    <collapsible title="Button group">
        <div class="button-group">
            <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', color]">
                @{{ color[0].toUpperCase() + color.slice(1) }}
            </button>
        </div>
    </collapsible>
</collapsible>

<collapsible title="Inputs" id="ui-inputs">
    <collapsible title="Default">
        <input type="text" class="voyager-input w-full" placeholder="Placeholder" />
    </collapsible>
    <collapsible title="Disabled">
            <input type="text" class="voyager-input w-full" disabled placeholder="Placeholder" />
    </collapsible>
    <collapsible title="Small">
        <input type="text" class="voyager-input w-full small" placeholder="Placeholder" />
    </collapsible>
    <collapsible title="With label">
        <label class="label" for="labeled-input">Label</label>
        <input type="text" class="voyager-input w-full" id="labeled-input" placeholder="Placeholder" />
    </collapsible>
    <collapsible title="Colors" :opened="false">
        <input v-for="color in $store.ui.colors" type="text" class="voyager-input w-full mb-2" :class="color" :placeholder="ucfirst(color)" :key="'input-'+color">
    </collapsible>
</collapsible>

<collapsible title="Select" id="ui-select">
    <collapsible title="Single">
        <select-input :options="$store.ui.select_options" v-model="$store.ui.selected_option"></select-input>
    </collapsible>
    <collapsible title="Multiple">
        <select-input :options="$store.ui.select_options" v-model="$store.ui.selected_options" :close-on-select="false" multiple></select-input>
    </collapsible>
</collapsible>

<collapsible title="Tag input" id="ui-tags">
    <tag-input v-model="$store.ui.tags"></tag-input>
</collapsible>

<collapsible title="WYSIWYG Editor" id="ui-wysiwyg">
    <wysiwyg></wysiwyg>
</collapsible>

<collapsible title="Tabs" id="ui-tabs">
    <tabs :tabs="[{name: 'tab1', title: 'Tab 1'}, {name: 'tab2', title: 'Tab 2'}, {name: 'tab3', title: 'Tab 3'}]">
        <div slot="tab1">
            <h3>Tab 1</h3>
            <p>@{{ $store.ui.lorem }}</p>
        </div>
        <div slot="tab2">
            <h3>Tab 2</h3>
            <p>@{{ $store.ui.lorem }}</p>
        </div>
        <div slot="tab3">
            <h3>Tab 3</h3>
            <p>@{{ $store.ui.lorem }}</p>
        </div>
    </tabs>
</collapsible>

<collapsible title="Badges" id="ui-badges">
    <collapsible title="Default">
        <badge v-for="color in $store.ui.colors" :color="color" :key="'badge-'+color">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </badge>
    </collapsible>
    <collapsible title="Large">
        <badge v-for="color in $store.ui.colors" :color="color" :key="'badge-'+color" class="large">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </badge>
    </collapsible>
</collapsible>

<collapsible title="Alerts" id="ui-alerts">
    <alert v-for="color in $store.ui.colors" :color="color" :key="'alert-'+color" class="mb-3">
        <span slot="title">@{{ color[0].toUpperCase() + color.slice(1) }}</span>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid pariatur, ipsum similique veniam quo totam eius aperiam dolorum.</p>
    </alert>
</collapsible>

<collapsible title="Notifications" id="ui-notifications">
    <collapsible v-for="color in $store.ui.colors" :key="'notification_'+color" :title="ucfirst(color)">
        <div class="inline-flex">
            <button @click="$notify.notify($store.ui.lorem, ucfirst(color), color)" class="button" :class="color">Message and title</button>
            <button @click="$notify.notify($store.ui.lorem, null, color)" class="button" :class="color">Message only</button>
            <button @click="$notify.notify($store.ui.lorem, ucfirst(color), color, null, true)" class="button" :class="color">Indeterminate</button>
            <button @click="$notify.notify($store.ui.lorem, ucfirst(color), color, 5000, false)" class="button" :class="color">With timeout</button>
        </div>
    </collapsible>
    <collapsible title="Confirm">
        <div class="inline-flex">
            <button @click="$notify.confirm('Are you sure?', function (result) {})" class="button blue">Simple</button>
            <button @click="$notify.confirm('Are you sure?', function (result) {}, null, 'blue', 'Yes', 'No', null, true)" class="button blue">Indeterminate</button>
            <button @click="$notify.confirm('Are you sure?', function (result) {}, null, 'blue', 'Yes', 'No', 5000)" class="button blue">With timeout</button>
            <button @click="$notify.confirm('Are you sure?', function (result) {}, null, 'blue', 'Of course', 'Nah')" class="button blue">Custom buttons</button>
        </div>
    </collapsible>
    <collapsible title="Prompt">
        <div class="inline-flex">
            <button @click="$notify.prompt('Enter your name', '', function (result) {})" class="button blue">Simple</button>
            <button @click="$notify.prompt('Enter your name', '', function (result) {}, 'blue', 'Save', 'Abort')" class="button blue">Custom buttons</button>
            <button @click="$notify.prompt('Enter your name', $store.ui.name, function (result) { if (result) { $store.ui.name = result; } })" class="button blue">Value: @{{ $store.ui.name }}</button>
        </div>
    </collapsible>
</collapsible>

<collapsible title="Scrollbars" id="ui-scrollbars">
    <alert color="yellow" class="mb-4">
        Hit Shift to scroll horizontal
    </alert>
    <collapsible title="Vertical">
        <scrollbar class="max-h-64" ref="Scrollbar">
            <div>
                <p v-for="i in 50" :key="i" class="whitespace-no-wrap">
                    Item #@{{ i }}
                </p>
            </div>
        </scrollbar>
    </collapsible>
    <collapsible title="Horizontal">
        <scrollbar class="max-h-64" ref="Scrollbar">
            <div>
                <p v-for="i in 5" :key="i" class="whitespace-no-wrap">
                    #@{{ i }} @{{ $store.ui.lorem }} @{{ $store.ui.lorem }} @{{ $store.ui.lorem }} @{{ $store.ui.lorem }}
                </p>
            </div>
        </scrollbar>
    </collapsible>
    <collapsible title="Both">
        <scrollbar class="max-h-64" ref="Scrollbar">
            <div>
                <p v-for="i in 25" :key="i" class="whitespace-no-wrap">
                    #@{{ i }} @{{ $store.ui.lorem }} @{{ $store.ui.lorem }} @{{ $store.ui.lorem }} @{{ $store.ui.lorem }}
                </p>
            </div>
        </scrollbar>
    </collapsible>
</collapsible>

<collapsible title="Pagination" id="ui-pagination">
<collapsible title="Default">
        <pagination :page-count="100" :value="1"></pagination>
    </collapsible>

    <collapsible title="No previous/next button">
        <pagination :page-count="100" :value="10" :prev-next-buttons="false"></pagination>
    </collapsible>
    
    <collapsible title="No first/last button">
        <pagination :page-count="100" :value="25" :first-last-buttons="false"></pagination>
    </collapsible>

    <collapsible title="Only page-buttons">
        <pagination :page-count="100" :value="50" :first-last-buttons="false" :prev-next-buttons="false"></pagination>
    </collapsible>

    <collapsible title="Different color (Works with all other colors as well)">
        <pagination :page-count="100" :value="100" color="red"></pagination>
    </collapsible>
</collapsible>

@endsection