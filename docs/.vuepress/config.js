import fs from 'fs'
import path from 'path'
import dotenv from 'dotenv'

import {viteBundler} from '@vuepress/bundler-vite'
import {defaultTheme} from '@vuepress/theme-default'
import {defineUserConfig} from 'vuepress'

dotenv.config()

function getChildren(folder, sort = 'asc') {
    const extension = ['.md']
    const names = ['index.md', 'readme.md']

    const dir = `${__dirname}/../${folder}`

    return fs
        .readdirSync(path.join(dir))
        .filter(item =>
            fs.statSync(path.join(dir, item)).isFile() &&
            !names.includes(item.toLowerCase()) &&
            extension.includes(path.extname(item))
        )
        .sort((a, b) => {
            a = resolveNumeric(a)
            b = resolveNumeric(b)

            if (a < b) return sort === 'asc' ? -1 : 1
            if (a > b) return sort === 'asc' ? 1 : -1

            return 0
        }).map(item => `/${folder}/${item}`)
}

function resolveNumeric(value) {
    const sub = value.substring(0, value.indexOf('.'))

    const num = Number(sub)

    return isNaN(num) ? value : num
}

const hostname = 'actions.dragon-code.pro'

export default defineUserConfig({
    bundler: viteBundler(),

    lang: 'en-US',
    title: 'Dragon Code: Actions',
    description: 'Performing actions with saving the list of called files',

    head: [
        ['link', {rel: 'icon', href: `https://${hostname}/images/logo.svg`}],
        ['meta', {name: 'twitter:image', content: `https://${hostname}/images/logo.svg`}]
    ],

    theme: defaultTheme({
        hostname,
        base: '/',

        logo: `https://${hostname}/images/logo.svg`,

        repo: 'https://github.com/TheDragonCode/laravel-actions',
        repoLabel: 'GitHub',
        docsRepo: 'https://github.com/TheDragonCode/laravel-actions',
        docsBranch: 'main',
        docsDir: 'docs',

        contributors: false,
        editLink: true,

        navbar: [
            {
                text: '5.x',
                children: [
                    {text: '5.x', link: '/getting-started/installation/index.md'},
                    {text: '4.x', link: 'https://github.com/TheDragonCode/laravel-actions/blob/4.x/docs/index.md'},
                    {text: '3.x', link: 'https://github.com/TheDragonCode/laravel-actions/blob/3.x/docs/index.md'},
                ]
            }
        ],

        sidebarDepth: 1,

        sidebar: [
            {
                text: 'Prologue',
                children: [
                    {
                        text: 'Upgrade Guide',
                        link: '/prologue/upgrade-guide/index.md'
                    },
                    {
                        text: 'License',
                        link: '/prologue/license.md'
                    }
                ]
            },

            {
                text: 'Getting Started',
                children: [
                    {
                        text: 'Installation',
                        link: '/getting-started/installation/index.md'
                    }
                ]
            },

            {
                text: 'How To Use',
                children: getChildren('how-to-use')
            },

            {
                text: 'Helpers',
                children: getChildren('helpers')
            }
        ]
    })
})
