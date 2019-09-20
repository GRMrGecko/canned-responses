//
// Copyright (c) 2019, Mr. Gecko's Media (James Coleman)
// All rights reserved.
//
// Redistribution and use in source and binary forms, with or without modification,
//    are permitted provided that the following conditions are met:
//
// 1. Redistributions of source code must retain the above copyright notice, this
//    list of conditions and the following disclaimer.
//
// 2. Redistributions in binary form must reproduce the above copyright notice,
//    this list of conditions and the following disclaimer in the documentation
//    and/or other materials provided with the distribution.
//
// 3. Neither the name of the copyright holder nor the names of its contributors
//    may be used to endorse or promote products derived from this software without
//    specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
//    ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
//    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
//    IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
//    INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
//    BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA,
//    OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
//    WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
//    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
//    POSSIBILITY OF SUCH DAMAGE.
//

import { ISlashCommand, ISlashCommandPreview, ISlashCommandPreviewItem, SlashCommandContext, SlashCommandPreviewItemType } from '@rocket.chat/apps-engine/definition/slashcommands';

import { IHttp, IModify, IPersistence, IRead } from '@rocket.chat/apps-engine/definition/accessors';

import { App } from '@rocket.chat/apps-engine/definition/App';

export class CannedResponsesSlashCommand implements ISlashCommand {
    public command = 'can';
    public i18nParamsExample = 'can_command_params_example';
    public i18nDescription = 'can_command_description';
    public providesPreview = true;

    constructor(private readonly app: App) { }

    public async executor(context: SlashCommandContext, read: IRead, modify: IModify, http: IHttp, persis: IPersistence): Promise<void> {
        const settingsReader = read.getEnvironmentReader().getSettings();
        const apiURL = await settingsReader.getValueById('canned_responses_api_url');
        const command = context.getArguments();

        if (command.length === 0 || command[0] === 'help') {
            return this.processHelpCommand(context, read, modify);
        }

        if (command[0] === 'list') {
            return this.processListCommand(context, read, modify, http);
        }

        const message = await modify.getCreator().startMessage();
        const sender = await read.getUserReader().getByUsername(context.getSender().username);

        const room = await read.getRoomReader().getById(context.getRoom().id);
        const roomEph = context.getRoom();

        if (!room) {
            throw Error('No room is configured for the message');
        }
        message.setRoom(room);
        message.setSender(sender);

        const key = command.join(' ');

        try {
            const result = await http.get(`${apiURL}response/get?key=${key}`);
            if (result.data.type === 'success') {
                const response = result.data.results[0];
                if (!response) {
                    throw new Error('No response found.');
                }
                message.setText(response.message);
                modify.getCreator().finish(message);
            } else {
                throw new Error('Could not get a response.');
            }
        } catch (error) {
            const rocketSender = await read.getUserReader().getById('rocket.cat');
            message.setSender(rocketSender);
            message.setRoom(roomEph);
            message.setText(error.message);
            modify.getNotifier().notifyRoom(roomEph, message.getMessage());
        }
    }

    public async previewer(context: SlashCommandContext, read: IRead, modify: IModify, http: IHttp, persis: IPersistence): Promise<ISlashCommandPreview> {
        const settingsReader = read.getEnvironmentReader().getSettings();
        const apiURL = await settingsReader.getValueById('canned_responses_api_url');
        const command = context.getArguments();

        if (command[0] === 'help') {
            return {
                i18nTitle: 'can_command_preview',
                items: [{
                    id: 'help',
                    type: SlashCommandPreviewItemType.TEXT,
                    value: 'Print help.',
                }],
            };
        }

        if (command[0] === 'list') {
            return {
                i18nTitle: 'can_command_preview',
                items: [{
                    id: 'list',
                    type: SlashCommandPreviewItemType.TEXT,
                    value: 'List all keys.',
                }],
            };
        }

        const key = command.join(' ');

        try {
            const result = await http.get(`${apiURL}response/find?key=${key}`);
            if (result.data.type === 'success') {
                return {
                    i18nTitle: 'can_command_preview',
                    items: result.data.results.map((response) => {
                        return {
                            id: response.docid,
                            type: SlashCommandPreviewItemType.TEXT,
                            value: response.message,
                        };
                    }),
                };
            } else {
                throw new Error('Could not get a response.');
            }
        } catch (error) {
            console.log(error.message);
        }
        return {
            i18nTitle: 'can_command_preview',
            items: [],
        };
    }

    // tslint:disable-next-line:max-line-length
    public async executePreviewItem(item: ISlashCommandPreviewItem, context: SlashCommandContext, read: IRead, modify: IModify, http: IHttp, persis: IPersistence): Promise<void> {
        if (item.id === 'help') {
            return this.processHelpCommand(context, read, modify);
        }
        if (item.id === 'list') {
            return this.processListCommand(context, read, modify, http);
        }

        const message = await modify.getCreator().startMessage();
        const sender = await read.getUserReader().getByUsername(context.getSender().username);

        const room = await read.getRoomReader().getById(context.getRoom().id);

        if (!room) {
            throw Error('No room is configured for the message');
        }

        message.setRoom(room);
        message.setSender(sender);

        message.setText(item.value);
        modify.getCreator().finish(message);
    }

    private async processHelpCommand(context: SlashCommandContext, read: IRead, modify: IModify): Promise<void> {
        const message = await modify.getCreator().startMessage();
        const sender = await read.getUserReader().getById('rocket.cat');
        const roomEph = context.getRoom();

        message.setSender(sender);
        message.setRoom(roomEph);

        const text =
        `These are the commands I can understand:
        \`/can help\` Get this help.
        \`/can list\` List all available keys.
        \`/can key\` Provide a canned response.`;
        message.setText(text);
        modify.getNotifier().notifyRoom(roomEph, message.getMessage());
    }

    private async processListCommand(context: SlashCommandContext, read: IRead, modify: IModify, http: IHttp): Promise<void> {
        const settingsReader = read.getEnvironmentReader().getSettings();
        const apiURL = await settingsReader.getValueById('canned_responses_api_url');
        const message = await modify.getCreator().startMessage();
        const sender = await read.getUserReader().getById('rocket.cat');
        const roomEph = context.getRoom();

        message.setSender(sender);
        message.setRoom(roomEph);

        let text = 'These are the keys available:\n';

        const result = await http.get(`${apiURL}response/get-all`);
        if (result.data.type === 'success') {
            let count = 0;
            result.data.results.forEach((response) => {
                if (count !== 0) {
                    text += ', ';
                }
                text += response.key;
                count++;
            });
        }

        message.setText(text);
        modify.getNotifier().notifyRoom(roomEph, message.getMessage());
    }
}
