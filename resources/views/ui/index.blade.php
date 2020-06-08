@extends('voyager::app')

@section('page-title', 'UI')

@section('content')
<card title="UI Elements">
    <div>
        <div class="inline w-full">
        <button class="button accent my-2" v-scroll-to="'ui-headings'">
                Headings
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-icons'">
                Icons
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-buttons'">
                Buttons
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-inputs'">
                Inputs
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-datetime'">
                Date/Time picker
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-listbox'">
                Listbox
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-tags'">
                Tag input
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-tabs'">
                Tabs
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-badges'">
                Badges
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-alerts'">
                Alerts
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-notifications'">
                Notifications
            </button>
            <button class="button accent my-2" v-scroll-to="'ui-pagination'">
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

<collapsible title="Icons" id="ui-icons">
    <icon-picker />
</collapsible>

<collapsible title="Buttons" id="ui-buttons">
    <collapsible title="Default" :title-size="5">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', color]">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Disabled" :title-size="5">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', color]" disabled>
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Small" :title-size="5">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'small', color]">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Large" :title-size="5">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'large', color]">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="With Icon" :title-size="5">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'small', color]">
            <icon icon="information-circle" class="mr-1"></icon>
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Responsive" :title-size="5">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'small', color]">
            <icon icon="information-circle"></icon>
            <span>@{{ color[0].toUpperCase() + color.slice(1) }}</span>
        </button>
    </collapsible>
    <collapsible title="Button group" :title-size="5">
        <div class="button-group">
            <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', color]">
                @{{ color[0].toUpperCase() + color.slice(1) }}
            </button>
        </div>
    </collapsible>
</collapsible>

<collapsible title="Inputs" id="ui-inputs">
    <collapsible title="Default" :title-size="5">
        <input type="text" class="input w-full" placeholder="Placeholder" />
    </collapsible>
    <collapsible title="Disabled" :title-size="5">
            <input type="text" class="input w-full" disabled placeholder="Placeholder" />
    </collapsible>
    <collapsible title="Small" :title-size="5">
        <input type="text" class="input w-full small" placeholder="Placeholder" />
    </collapsible>
    <collapsible title="With label" :title-size="5">
        <label class="label" for="labeled-input">Label</label>
        <input type="text" class="input w-full" id="labeled-input" placeholder="Placeholder" />
    </collapsible>
    <collapsible title="Colors" :opened="false" :title-size="5">
        <input v-for="color in $store.ui.colors" type="text" class="input w-full mb-2" :class="color" :placeholder="ucfirst(color)" :key="'input-'+color">
    </collapsible>
</collapsible>

<collapsible title="Date/Time picker" id="ui-datetime">
    <div class="flex">
        <card class="w-1/3" title="Date" :title-size="6">
            <date-time />
        </card>
        <card class="w-1/3" title="Date and time" :title-size="6">
            <date-time select-time format="YYYY-MM-DD HH:mm" />
        </card>
        <card class="w-1/3" title="Date and time with second" :title-size="6">
            <date-time select-time select-seconds format="YYYY-MM-DD HH:mm:ss" />
        </card>
    </div>
    <div class="flex">
        <card class="w-1/3" title="Date and time 12 hours" :title-size="6">
            <date-time select-time am-pm format="YYYY-MM-DD hh:mm A" />
        </card>
        <card class="w-1/3" title="Date range" :title-size="6">
            <date-time format="YYYY-MM-DD" range />
        </card>
        <card class="w-1/3" title="Date and time range" :title-size="6">
            <date-time format="YYYY-MM-DD HH:mm" range select-time />
        </card>
    </div>
    <div class="flex">
        <card class="w-1/2" title="Date and time range with seconds" :title-size="6">
            <date-time select-time select-seconds range format="YYYY-MM-DD HH:mm:ss" />
        </card>
        <card class="w-1/2" title="Date and time range 12 hours" :title-size="6">
            <date-time select-time am-pm range format="YYYY-MM-DD hh:mm A" />
        </card>
    </div>
</collapsible>

<collapsible title="Listbox" id="ui-listbox">
<collapsible title="Single" :title-size="5">
        <listbox :options="$store.ui.select_options" v-model="$store.ui.selected_option"></listbox>
    </collapsible>
    <collapsible title="Multiple" :title-size="5">
        <listbox :options="$store.ui.select_options" v-model="$store.ui.selected_options" :close-on-select="false"></listbox>
    </collapsible>
</collapsible>

<collapsible title="Tag input" id="ui-tags">
    <tag-input v-model="$store.ui.tags"></tag-input>
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
    <collapsible title="Default" :title-size="5">
        <badge v-for="color in $store.ui.colors" :color="color" :key="'badge-'+color">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </badge>
    </collapsible>
    <collapsible title="Large" :title-size="5">
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
    <collapsible v-for="color in $store.ui.colors" :key="'notification_'+color" :title="ucfirst(color)" :title-size="5">
        <div class="inline-flex">
            <button @click="new $notification($store.ui.lorem).title(ucfirst(color)).color(color).show()" class="button" :class="color">Message and title</button>
            <button @click="new $notification($store.ui.lorem).color(color).show()" class="button" :class="color">Message only</button>
            <button @click="new $notification($store.ui.lorem).title(ucfirst(color)).color(color).indeterminate().show()" class="button" :class="color">Indeterminate</button>
            <button @click="new $notification($store.ui.lorem).title(ucfirst(color)).color(color).timeout().show()" class="button" :class="color">With timeout</button>
        </div>
    </collapsible>
    <collapsible title="Confirm" :title-size="5">
        <div class="inline-flex">
            <button @click="new $notification('Are you sure?').confirm().show().then((r) => {})" class="button blue">Simple</button>
            <button @click="new $notification('Are you sure?').confirm().indeterminate().show()" class="button blue">Indeterminate</button>
            <button @click="new $notification('Are you sure?').confirm().timeout().show()" class="button blue">With timeout</button>
            <button @click="new $notification('Are you sure?').confirm().addButton({key: true, value: 'Yup', color: 'green'}).addButton({key: false, value: 'Nah', color: 'red'}).show()" class="button blue">Custom buttons</button>
        </div>
    </collapsible>
    <collapsible title="Prompt" :title-size="5">
        <div class="inline-flex">
            <button @click="new $notification('Enter your name').prompt('').show()" class="button blue">Simple</button>
            <button @click="new $notification('Enter your name').prompt('').timeout().show()" class="button blue">With timeout</button>
            <button @click="new $notification('Enter your name').prompt('').addButton({key: true, value: 'Safe', color: 'green'}).addButton({key: false, value: 'Abort', color: 'red'}).show()" class="button blue">Custom buttons</button>
            <button @click="new $notification('Enter your name').prompt($store.ui.name).show().then((r) => { if (r !== false) { $store.ui.name = r; } })" class="button blue">Value: @{{ $store.ui.name }}</button>
        </div>
    </collapsible>
</collapsible>

<collapsible title="Pagination" id="ui-pagination">
    <collapsible title="Default" :title-size="5">
        <pagination :page-count="100" :value="1"></pagination>
    </collapsible>

    <collapsible title="No previous/next button" :title-size="5">
        <pagination :page-count="100" :value="10" :prev-next-buttons="false"></pagination>
    </collapsible>
    
    <collapsible title="No first/last button" :title-size="5">
        <pagination :page-count="100" :value="25" :first-last-buttons="false"></pagination>
    </collapsible>

    <collapsible title="Only page-buttons" :title-size="5">
        <pagination :page-count="100" :value="50" :first-last-buttons="false" :prev-next-buttons="false"></pagination>
    </collapsible>

    <collapsible title="Different color (Works with all other colors as well)" :title-size="5">
        <pagination :page-count="100" :value="100" color="red"></pagination>
    </collapsible>
</collapsible>

@endsection