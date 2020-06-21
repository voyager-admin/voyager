<template>
    <div>
        <input type="text" class="input w-full mb-3" :placeholder="__('voyager::generic.search_icons')" v-model="query" />
        <div class="grid grid-cols-12 gap-1">
            <button
                class="button accent icon-only justify-center my-1"
                v-for="(icon, i) in filteredIcons.slice(start, end)"
                :key="'icon-' + i"
                @dblclick="$emit('select', icon.name)"
                v-tooltip="icon.readable">
                <icon :icon="icon.name" :size="6" />
            </button>
        </div>
        <pagination class="mt-2" :page-count="pages" v-on:input="page = $event - 1" v-bind:value="page + 1" :first-last-buttons="false" :prev-next-buttons="false"></pagination>
    </div>
</template>
<script>
var icons = [
    "Adjustments","Annotation","Archive","ArrowCircleDown","ArrowCircleLeft","ArrowCircleRight","ArrowCircleUp",
    "ArrowDown","ArrowLeft","ArrowNarrowDown","ArrowNarrowLeft","ArrowNarrowRight","ArrowNarrowUp","ArrowRight",
    "ArrowUp","ArrowsExpand","AtSymbol","BadgeCheck","Ban","Bell","BookOpen","BookmarkAlt","Bookmark","Briefcase",
    "Calendar","Camera","Cash","ChartBar","ChartPie","ChartSquareBar","ChatAlt-2","ChatAlt","Chat","CheckCircle",
    "Check","ChevronDown","ChevronLeft","ChevronRight","ChevronUp","ClipboardCheck","ClipboardCopy","ClipboardList",
    "Clipboard","Clock","CloudDownload","CloudUpload","Code","Cog","Collection","ColorSwatch","CreditCard","CurrencyDollar",
    "CurrencyEuro","CurrencyPound","CurrencyRupee","CurrencyYen","CursorClick","DesktopComputer","DocumentAdd","DocumentDownload",
    "DocumentDuplicate","DocumentRemove","DocumentReport","Document","DotsCircleHorizontal","DotsHorizontal","DotsVertical",
    "Download","Duplicate","EmojiHappy","EmojiSad","ExclamationCircle","Exclamation","ExternalLink","EyeOff","Eye","Filter",
    "Fire","Flag","FolderAdd","FolderDownload","FolderRemove","Folder","GlobeAlt","Globe","Hand","Hashtag","Heart","Home",
    "InboxIn","Inbox","InformationCircle","Key","Library","LightBulb","LightningBolt","Link","LocationMarker","LockClosed",
    "LockOpen","Logout","MailOpen","Mail","MenuAlt-1","MenuAlt-2","MenuAlt-3","MenuAlt-4","Menu","Microphone","MinusCircle",
    "Moon","Newspaper","OfficeBuilding","PaperClip","Pause","PencilAlt","Pencil","PhoneIncoming","PhoneOutgoing","Phone",
    "Photograph","Play","PlusCircle","Plus","Printer","Puzzle","Qrcode","QuestionMarkCircle","ReceiptRefund","Refresh","Reply",
    "Scale","Search","Selector","Share","ShieldCheck","ShieldExclamation","ShoppingBag","ShoppingCart","SortAscending","SortDescending",
    "Sparkles","Speakerphone","Star","Stop","Sun","Support","SwitchHorizontal","SwitchVertical","Tag","Template","Terminal","ThumbDown",
    "ThumbUp","Ticket","Translate","Trash","TrendingDown","TrendingUp","Upload","UserAdd","UserCircle","UserGroup","UserRemove","User",
    "Users","ViewBoards","ViewGridAdd","ViewGrid","ViewList","VolumeOff","VolumeUp","XCircle","X","ZoomIn","ZoomOut"
];

export default {
    data: function () {
        return {
            query: '',
            page: 0,
            resultsPerPage: 120,
        };
    },
    methods: {
        selectIcon: function (icon) {
            this.$emit('select', icon);
        },
    },
    computed: {
        start: function () {
            return this.page * this.resultsPerPage;
        },
        end: function () {
            return this.start + this.resultsPerPage;
        },
        pages: function () {
            return Math.ceil(this.filteredIcons.length / this.resultsPerPage);
        },
        filteredIcons: function () {
            var vm = this;
            var q = vm.query.toLowerCase();
            return icons.whereLike(q).map(function (icon) {
                return {
                    name: vm.kebab_case(icon),
                    readable: icon,
                }
            });
        },
    },
    watch: {
        query: function (q) {
            this.page = 0;
        }
    }
};
</script>