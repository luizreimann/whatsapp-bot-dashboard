import StartNode from './StartNode';
import MessageNode from './MessageNode';
import QuestionNode from './QuestionNode';
import ConditionNode from './ConditionNode';
import SwitchNode from './SwitchNode';
import ActionNode from './ActionNode';
import IntegrationNode from './IntegrationNode';
import DelayNode from './DelayNode';
import EndNode from './EndNode';
import MediaNode from './MediaNode';
import LocationNode from './LocationNode';
import ContactNode from './ContactNode';
import ReactionNode from './ReactionNode';
import RandomNode from './RandomNode';
import BusinessHoursNode from './BusinessHoursNode';
import VariableNode from './VariableNode';
import WebhookNode from './WebhookNode';
import TransferNode from './TransferNode';

export const nodeTypes = {
    start: StartNode,
    message: MessageNode,
    question: QuestionNode,
    condition: ConditionNode,
    switch: SwitchNode,
    action: ActionNode,
    integration: IntegrationNode,
    delay: DelayNode,
    end: EndNode,
    media: MediaNode,
    location: LocationNode,
    contact: ContactNode,
    reaction: ReactionNode,
    random: RandomNode,
    businessHours: BusinessHoursNode,
    variable: VariableNode,
    webhook: WebhookNode,
    transfer: TransferNode,
};

export {
    StartNode,
    MessageNode,
    QuestionNode,
    ConditionNode,
    SwitchNode,
    ActionNode,
    IntegrationNode,
    DelayNode,
    EndNode,
    MediaNode,
    LocationNode,
    ContactNode,
    ReactionNode,
    RandomNode,
    BusinessHoursNode,
    VariableNode,
    WebhookNode,
    TransferNode,
};
