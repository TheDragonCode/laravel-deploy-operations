import fs from 'fs'
import path from 'path'
import dotenv from 'dotenv'

import { defaultTheme, viteBundler } from 'vuepress'

dotenv.config()

const hostname = 'actions.dragon-code.pro'

module.exports = {
    lang: 'en-US',
    title: 'Dragon Code: Actions',
    description: 'Performing actions with saving the list of called files',

    head: [
        ['link', { rel: 'icon', href: `https://${ hostname }/images/logo.svg` }],
        ['meta', { name: 'twitter:image', content: `https://${ hostname }/images/logo.svg` }]
    ],

    bundler: viteBundler(),

    theme: defaultTheme({
        hostname,
        base: '/',

        logo: `https://${ hostname }/images/logo.svg`,

        repo: 'https://github.com/TheDragonCode/laravel-migration-actions',
        repoLabel: 'GitHub',
        docsRepo: 'https://github.com/TheDragonCode/laravel-migration-actions',
        docsBranch: 'main',
        docsDir: 'docs',

        contributors: false,
        editLink: true,

        navbar: [
            { text: '3.x', link: '/prologue/changelog/index.md' }
        ],

        sidebarDepth: 1,

        sidebar: [
            {
                text: 'Prologue',
                collapsible: true,
                children: [
                    {
                        text: 'Upgrade Guide',
                        link: '/prologue/upgrade.md'
                    },
                    {
                        text: 'License',
                        link: '/prologue/license.md'
                    },
                    {
                        text: 'Changelog',
                        link: '/prologue/changelog/index.md',
                        collapsible: true,
                        children: getChildren('prologue/changelog', 'desc')
                    }
                ]
            },

            {
                text: 'Getting Started',
                collapsible: true,
                children: [
                    {
                        text: 'Installation',
                        link: '/getting-started/installation/index.md'
                    }
                ]
            },

            {
                text: 'How To Use',
                collapsible: true,
                children: getChildren('how-to-use')
            },

            {
                text: 'Helpers',
                collapsible: true,
                children: getChildren('helpers')
            }
        ]
    })
}

function getChildren(folder, sort = 'asc') {
    const extension = ['.md']
    const names = ['index.md', 'readme.md']

    const dir = `${ __dirname }/../${ folder }`

    return fs
        .readdirSync(path.join(dir))
        .filter(item =>
            fs.statSync(path.join(dir, item)).isFile() &&
            ! names.includes(item.toLowerCase()) &&
            extension.includes(path.extname(item))
        )
        .sort((a, b) => {
            a = resolveNumeric(a)
            b = resolveNumeric(b)

            if (a < b) return sort === 'asc' ? -1 : 1
            if (a > b) return sort === 'asc' ? 1 : -1

            return 0
        }).map(item => `/${ folder }/${ item }`)
}

function resolveNumeric(value) {
    const sub = value.substring(0, value.indexOf('.'))

    const num = Number(sub)

    return isNaN(num) ? value : num
}
